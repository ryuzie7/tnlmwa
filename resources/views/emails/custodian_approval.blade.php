<!DOCTYPE html>
<html>
<head>
    <title>Custodian Approval Notification</title>
</head>
<body>
    <h3>Dear {{ $custodianName }},</h3>
    @if($status === 'approved')
        <p>Your registration as a custodian has been approved.</p>
    @else
        <p>Your registration as a custodian has been rejected.</p>
    @endif
</body>
</html>
