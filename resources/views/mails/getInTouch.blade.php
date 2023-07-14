<!-- resources/views/emails/send-email.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Email</title>
</head>
<body>
    <h1>{{ $data['subject'] }}</h1>
    <p>Name: {{ $data['name'] }}</p>
    <p>Email: {{ $data['email'] }}</p>
    <p>{{ $data['body'] }}</p>
</body>
</html>
