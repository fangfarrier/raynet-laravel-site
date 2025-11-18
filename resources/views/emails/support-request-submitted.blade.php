<h1>New RAYNET Support Request</h1>

<p>A new support request has been submitted via the Liverpool RAYNET website.</p>

<h2>Event details</h2>
<ul>
    <li><strong>Event name:</strong> {{ $data['event_name'] ?? '-' }}</li>
    <li><strong>Event date:</strong> {{ $data['event_date'] ?? '-' }}</li>
    <li><strong>Location:</strong> {{ $data['location'] ?? '-' }}</li>
    <li><strong>Organising body:</strong> {{ $data['org'] ?? '-' }}</li>
</ul>

<h2>Contact</h2>
<ul>
    <li><strong>Name:</strong> {{ $data['contact_name'] ?? '-' }}</li>
    <li><strong>Email:</strong> {{ $data['contact_email'] ?? '-' }}</li>
    <li><strong>Phone:</strong> {{ $data['contact_phone'] ?? '-' }}</li>
</ul>

<h2>Outline / how RAYNET can help</h2>
<p>{!! nl2br(e($data['details'] ?? '')) !!}</p>

<p>— Liverpool RAYNET (10/ME/179/)</p>