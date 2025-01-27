<h1>Testing [Your Invoice] only</h1>

<p>Dear {{ $license->applicant->first_name }} {{ $license->applicant->last_name }},</p>

<p>Please find attached your invoice for license number: {{ $license->invoice_number }}</p>

<h2>Invoice Details:</h2>


<p>To view or download your full invoice, please <a href="">click here</a>.</p>

<h2>Payment Methods:</h2>


<p>If you have any questions about this invoice, please contact our billing department at .</p>

<p>Thank you for your business!</p>

<p>Best regards,<br>
</p>