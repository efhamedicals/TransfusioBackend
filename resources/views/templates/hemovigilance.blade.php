<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fiche d'hémovigilance</title>
    <style>
        .main {
            width: 100%;
            margin: auto;
            padding: 10px
        }

        .title {
            text-align: center;
            background-color: black;
            color: white;
            padding: 10px;
            margin: auto;
            width: 30%;
            border-radius: 10px
        }

        #infos-perso {
            width: 100%;
        }

        #infos-perso .td {
            width: 50%
        }
    </style>
</head>

<body>
    <div class="main">
        <h2 class="title">Fiche d'hémovigilance</h2>
        <br>
        Numéro de la fiche : <b>{{ $transfusion->reference }}</b>
        <h3>1 - Informations sur le malade receveur</h3>
        <table id="infos-perso">
            <tr>
                <td>Nom : <b>{{ $transfusion->prescription->patient->last_name }}</b></td>
                <td>Prénoms : <b>{{ $transfusion->prescription->patient->first_name }}</b></td>
            </tr>
            <tr>
                <td>Sexe : <b>{{ $transfusion->prescription->patient->gender }}</b></td>
                <td>Date de naissance : <b>{{ reformatDate($transfusion->prescription->patient->birth) }}</b></td>
            </tr>
            <tr>
                <td>Age : <b>{{ Date('Y') - explode('-', $transfusion->prescription->patient->birth)[0] }} ans</b></td>
                <td>Adresse : ---</td>
            </tr>
            <tr>
                <td>Email : <b>{{ $transfusion->prescription->patient->email }}</b></td>
                <td>Numéro de téléphone personnel : <b>{{ $transfusion->prescription->patient->phone }}</b></td>
            </tr>
            <tr>
                <td>Groupe sanguin rhésus : <b>{{ $transfusion->prescription->patient->blood_type }}
                        {{ $transfusion->prescription->patient->rhesus }}</b></td>
                <td></td>
            </tr>
        </table>
        @foreach ($transfusion->prescription->products as $prescriptionProduct)
            <h3>{{ $loop->index + 2 }} - Informations sur le produit sanguin N<sup>o</sup> {{ $loop->index + 1 }}</h3>
            <h4>{{ $loop->index + 2 }}.1 - Caractéristiques de la poche</h4>
            <ul>
                <li>Type : <b>{{ $transfusion->prescription->bloodBag->typeProductBlood->name }}</b></li>
                <li>ID de la poche : <b>{{ $transfusion->prescription->bloodBag->reference }}</b></li>
                <li>Groupe sanguin Rhésus : <b>{{ $transfusion->prescription->bloodBag->typeBlood->name }}</b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.2 - Informations sur la cession</h4>
            <ul>
                <li>Date et heure : <b>{{ $transfusion->prescription->bloodBag->typeProductBlood->name }}</b></li>
                <li>Banque de sang : <b>{{ $transfusion->prescription->bloodBag->bloodBank->hospital->short_name }}</b>
                </li>
                <li>Agent responsable :
                    <b>{{ $transfusion->prescription->bloodBag->bloodBank->employees[0]->last_name . ' ' . $transfusion->prescription->bloodBag->bloodBank->employees[0]->first_name }}</b>
                </li>
            </ul>
            <h4>{{ $loop->index + 2 }}.3 - Informations sur le transport</h4>
            <ul>
                <li>Hôpital de destination : <b></b></li>
                <li>Type de transport : <b></b></li>
                <li>Conditions de transport : <b></b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.4 - Informations sur la livraison</h4>
            <ul>
                <li>Hôpital : <b></b></li>
                <li>Service : <b></b></li>
                <li>Date et heure de réception : <b></b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.5 - Informations sur la prescription</h4>
            <ul>
                <li>Prescripteur : <b></b></li>
                <li>Catégorie : <b></b></li>
                <li>Prescription fait par délégation : <b>NON</b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.6 - Informations sur le test pré-transfusionnel (test au lit du malade)</h4>
            <ul>
                <li>Test fait : <b>OUI</b></li>
                <li>Date et heure de réalisation du test : <b></b></li>
                <li>Nom et prénoms : <b></b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.7 - Informations sur la transfusion</h4>
            <ul>
                <li>Agent : <b></b></li>
                <li>Date et heure du début : <b>{{ formatDate($transfusion->start_transfusion) }}</b></li>
                <li>Date et Heure de fin : <b>{{ formatDate($transfusion->end_transfusion) }}</b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.8 - Constantes avant début de la transfusion</h4>
            <ul>
                <li>HGB : <b></b></li>
                <li>TEMP : <b></b></li>
                <li>BPM : <b></b></li>
                <li>CPM : <b></b></li>
                <li>TAS : <b></b></li>
                <li>TAD : <b></b></li>
                <li>DIE : <b></b></li>
                <li>URI : <b></b></li>
                <li>PRU : <b></b></li>
            </ul>
            <h4>{{ $loop->index + 2 }}.9 - Constantes 5 minutes après le début de la transfusion</h4>
            <h4>{{ $loop->index + 2 }}.10 - Constantes 45 minutes après la fin de la transfusion</h4>
            <h4>{{ $loop->index + 2 }}.11 - Réactions transfusionnelles liées à cette poche</h4>
        @endforeach


    </div>
</body>

</html>
