<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BloodBagSeeder extends Seeder
{
    /**
     * Seeder pour les poches de sang.
     *
     * Caractéristiques d'une poche de sang :
     * - blood_center_id  : Centre de transfusion sanguine d'origine (obligatoire)
     * - blood_bank_id    : Banque de sang hospitalière (nullable – null si pas encore transféré)
     * - type_product_blood_id : Type de produit sanguin (Culot globulaire, PFC, Plaquettes, Sang total)
     * - type_blood_id    : Groupe sanguin ABO/Rhésus (nullable pour le plasma)
     * - reference        : Identifiant unique de la poche
     * - price            : Prix en FCFA
     * - date_expiration  : Date limite d'utilisation (variable selon produit)
     * - format           : 1 = Adulte | 2 = Pédiatrique
     * - status           : 0 = Disponible au CTS | 1 = Transféré en banque de sang | 2 = Assigné à une prescription
     */
    public function run(): void
    {
        // Récupération dynamique des IDs disponibles en base
        $bloodCenterIds = DB::table('blood_centers')->pluck('id')->toArray();
        $bloodBankIds   = DB::table('blood_banks')->pluck('id')->toArray();

        if (empty($bloodCenterIds)) {
            $this->command->warn('[BloodBagSeeder] Aucun centre de transfusion trouvé. Abandon du seeder.');
            return;
        }

        // ---------------------------------------------------------------
        // Données de référence
        // ---------------------------------------------------------------

        // Groupes sanguins : id => [nom, fréquence relative dans la population africaine]
        $typeBloodDistribution = [
            1 => ['name' => 'A+',  'weight' => 20],
            2 => ['name' => 'A-',  'weight' => 3],
            3 => ['name' => 'B+',  'weight' => 25],
            4 => ['name' => 'B-',  'weight' => 3],
            5 => ['name' => 'AB+', 'weight' => 5],
            6 => ['name' => 'AB-', 'weight' => 1],
            7 => ['name' => 'O+',  'weight' => 38],
            8 => ['name' => 'O-',  'weight' => 5],
        ];

        // Produits sanguins : id => [nom, durée_validité_jours, prix_FCFA, groupe_requis]
        // Le plasma frais congelé (PFC) n'exige pas de typage ABO strict → type_blood_id nullable
        $productConfig = [
            1 => [
                'name'             => 'Culot globulaire',
                'validity_days'    => 42,   // 6 semaines à +4°C
                'price_min'        => 15000,
                'price_max'        => 25000,
                'needs_blood_type' => true,
                'weight'           => 40,
            ],
            2 => [
                'name'             => 'Plasma frais congelé',
                'validity_days'    => 365,  // 1 an à −20°C
                'price_min'        => 10000,
                'price_max'        => 18000,
                'needs_blood_type' => false, // le PFC est souvent ABO-compatible universel
                'weight'           => 25,
            ],
            3 => [
                'name'             => 'Concentrés de standards de plaquettes',
                'validity_days'    => 5,    // 5 jours à +22°C sous agitation
                'price_min'        => 20000,
                'price_max'        => 35000,
                'needs_blood_type' => true,
                'weight'           => 20,
            ],
            4 => [
                'name'             => 'Sang total',
                'validity_days'    => 21,   // 3 semaines à +4°C
                'price_min'        => 12000,
                'price_max'        => 20000,
                'needs_blood_type' => true,
                'weight'           => 15,
            ],
        ];

        // Distribution des formats (adulte majoritaire)
        $formatDistribution = [
            1 => 75, // Adulte
            2 => 25, // Pédiatrique
        ];

        // Distribution des statuts
        // 0 = Disponible au CTS (pas encore transféré)
        // 1 = Transféré et disponible en banque de sang
        // 2 = Assigné à une prescription
        $statusDistribution = [
            0 => 40,
            1 => 45,
            2 => 15,
        ];

        // ---------------------------------------------------------------
        // Construction des tables de probabilité pondérée
        // ---------------------------------------------------------------
        $bloodTypePool   = $this->buildWeightedPool($typeBloodDistribution);
        $productPool     = $this->buildWeightedPool($productConfig);
        $formatPool      = $this->buildWeightedPool($formatDistribution);
        $statusPool      = $this->buildWeightedPool($statusDistribution);

        // ---------------------------------------------------------------
        // Génération des 120 poches
        // ---------------------------------------------------------------
        $bags = [];
        $now  = Carbon::now();
        $year = $now->year;

        for ($i = 1; $i <= 120; $i++) {
            $productId     = $productPool[array_rand($productPool)];
            $product       = $productConfig[$productId];
            $bloodTypeId   = $product['needs_blood_type']
                ? $bloodTypePool[array_rand($bloodTypePool)]
                : null;
            $format        = $formatPool[array_rand($formatPool)];
            $status        = $statusPool[array_rand($statusPool)];

            // Référence unique : BB-{ANNÉE}-{PRODUIT}-{SÉQUENCE_PADDED}
            // Exemple : BB-2026-CG-00042
            $productCode   = match ($productId) {
                1 => 'CG',
                2 => 'PFC',
                3 => 'CP',
                4 => 'ST',
            };
            $reference = sprintf('BB-%d-%s-%05d', $year, $productCode, $i);

            // Date d'expiration : on part d'une date de collecte fictive dans les 60 derniers jours
            $collectedDaysAgo  = rand(0, 60);
            $collectedAt       = $now->copy()->subDays($collectedDaysAgo);
            $expirationDate    = $collectedAt->copy()->addDays($product['validity_days']);

            // Prix aléatoire dans la fourchette du produit
            $price = rand(
                intdiv($product['price_min'], 500),
                intdiv($product['price_max'], 500)
            ) * 500;

            // blood_bank_id : présent uniquement si la poche est déjà transférée (status 1 ou 2)
            $bloodBankId = ($status >= 1 && !empty($bloodBankIds))
                ? $bloodBankIds[array_rand($bloodBankIds)]
                : null;

            $bloodCenterId = $bloodCenterIds[array_rand($bloodCenterIds)];

            $bags[] = [
                'blood_center_id'     => $bloodCenterId,
                'blood_bank_id'       => $bloodBankId,
                'type_product_blood_id' => $productId,
                'type_blood_id'       => $bloodTypeId,
                'reference'           => $reference,
                'price'               => $price,
                'date_expiration'     => $expirationDate->toDateString(),
                'format'              => $format,
                'status'              => $status,
                'created_at'          => $collectedAt->toDateTimeString(),
                'updated_at'          => $collectedAt->toDateTimeString(),
            ];
        }

        // Insertion par lots de 50 pour les performances
        foreach (array_chunk($bags, 50) as $chunk) {
            DB::table('blood_bags')->insert($chunk);
        }

        $this->command->info('[BloodBagSeeder] 120 poches de sang insérées avec succès.');

        // Résumé par produit
        $summary = array_count_values(array_column($bags, 'type_product_blood_id'));
        foreach ($summary as $productId => $count) {
            $this->command->line(sprintf(
                '  %-40s : %d poches',
                $productConfig[$productId]['name'],
                $count
            ));
        }

        // Résumé par statut
        $statusLabels = [0 => 'Disponible (CTS)', 1 => 'En banque de sang', 2 => 'Assigné'];
        $statusSummary = array_count_values(array_column($bags, 'status'));
        foreach ($statusSummary as $status => $count) {
            $this->command->line(sprintf('  %-40s : %d poches', $statusLabels[$status], $count));
        }
    }

    /**
     * Construit une liste plate pondérée à partir d'un tableau associatif
     * dont chaque valeur possède une clé 'weight' (ou la valeur elle-même est le poids).
     *
     * @param  array<int, array|int>  $items
     * @return array<int, int>
     */
    private function buildWeightedPool(array $items): array
    {
        $pool = [];
        foreach ($items as $id => $data) {
            $weight = is_array($data) ? $data['weight'] : $data;
            for ($j = 0; $j < $weight; $j++) {
                $pool[] = $id;
            }
        }
        return $pool;
    }
}
