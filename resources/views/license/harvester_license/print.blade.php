<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (styles remain unchanged) ... -->
</head>
<body>

<div class="row">
    <!-- ... (logo and title section remain unchanged) ... -->
</div>

<div class="row">
    <div class="columnDetails">
        <p class="section">PART I: APPLICANT DETAILS</p>
        <div class="border">
        <ol>
            <li><strong>Applicant Name:</strong> {{ $harvesterDetails->applicant_name }}</li>
            <li><strong>Applicant Type:</strong> {{ $harvesterDetails->applicant_type }}</li>
            <li><strong>National ID:</strong> {{ $harvesterDetails->national_id }}</li>
            <li><strong>Payment Receipt:</strong> {{ $harvesterDetails->payment_receipt }}</li>
            <li><strong>Local Buyer:</strong> {{ $harvesterDetails->local_buyer }}</li>
            <li><strong>Harvester Type:</strong> {{ $harvesterDetails->harvester_type }}</li>
            <li><strong>Fee:</strong> {{ $harvesterDetails->fee }}</li>
            <li><strong>Group Name:</strong> {{ $harvesterDetails->group_name }}</li>
            <li><strong>Group Members:</strong> {{ $harvesterDetails->group_members }}</li>
            <li><strong>Targeted Species:</strong>
                @if($commonNames)
                    {{ $commonNames }}
                @else
                    No targeted species found.
                @endif
            </li>
        </ol>
        </div>

        <p class="section">PART II: AUTHORISED ACTIVITY, AREA OF OPERATION</p>
        <div class="border">
            <ol>
                <li><strong>Authorised Activity:</strong> {{ $harvesterDetails->fishing_method }}</li>
                <li><strong>Area of Operation:</strong> {{ $harvesterDetails->island_name }}</li>
            </ol>
        </div>
    </div>

    <div class="columnConditions">
        <p class="section">PART III: LICENSE CONDITIONS</p>
        <div class="conditions border">
            <ol>
                <li>The permit holder must comply with all provisions of the Sea Cucumber Regulations 2024 and the Fisheries Act 2010.</li>
                <li>The permit is valid for a specified duration, and harvesting activities must occur within this timeframe.</li>
                <li>The permit holder must adhere to specific quotas for the quantity of sea cucumbers that can be harvested, as stipulated in the permit.</li>
                <li>The permit holder is prohibited from using scuba diving or any other underwater breathing apparatus for harvesting sea cucumbers.</li>
                <li>The permit holder must notify the Director of Fisheries or authorized officers of the harvesting location and schedule prior to commencing activities.</li>
                <li>The permit holder must submit regular reports on the quantity and species of sea cucumbers harvested to the Director of Fisheries.</li>
                <li>The permit holder must allow authorized officers access to the harvesting site and any related premises for inspections and compliance checks.</li>
                <li>The permit holder must ensure that any sale of harvested sea cucumbers is made only to licensed buyers and must provide evidence of such sales upon request.</li>
                <li>The permit holder must undertake harvesting practices that minimize damage to the marine environment and other marine life.</li>
                <li>The permit holder must maintain accurate records of all harvesting activities, including dates, quantities harvested, and species caught.</li>
                <li>The permit holder is not authorized to process sea cucumbers unless explicitly stated in the permit.</li>
                <li>The permit holder must ensure compliance with any additional local regulations and requirements imposed by the relevant authorities or island councils.</li>
                <li>The permit may be revoked or suspended if any conditions are violated or if the permit holder is found to be in breach of the Fisheries Act 2010 or these Regulations.</li>
            </ol>
        </div>
    </div>
</div>
</div>

</body>
</html>