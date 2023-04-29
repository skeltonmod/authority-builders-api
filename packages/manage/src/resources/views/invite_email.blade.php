Greetings {{$email}}, You have been invited to register here: 
<a href="{{ $link = route('app').'/register' . '?invited_user=' . urlencode($email).'&token='. urlencode($token) }}">{{ $link }}</a>
