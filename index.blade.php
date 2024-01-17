<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js" integrity="sha512-tXL5mrkSoP49uQf2jO0LbvzMyFgki//znmq0wYXGq94gVF6TU0QlrSbwGuPpKTeN1mIjReeqKZ4/NJPjHN1d2Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title>Document</title>
</head>
<body>
    <div class="chat">
        <div class="top">
            <div>
                <p>Rose</p>
                <small>Online</small>
            </div>
        </div>
        <div class="message">
            @include('receive',['message'=>"Hey"])
        </div>
        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" autocomplete="off">
                <button type="submit"></button>
            </form>
        </div>
    </div>
    <script>
    const pusher=new Pusher('{{config('broadcasting.connections.pusher.key')}}',{cluster:'ap2' });
    const channel=pusher.subscribe('public');
    channel.bind('chat',function(data){
        $.post("/receive",{
            _token:'{{csrf_token()}}',
            message:data.message,
        }).done(function(res){
            $(".message > .message").last().after(res);
            $(document).scrollTop($(document).height())
        });
    });

    $("form").submit(function(event){
        event.preventDefault();
        $.ajax({
            url:"/send",
            method:"post",
            headers:{
                'X-Socket-Id':pusher.connection.socket_id
            },
            data:{
                _token:'{{csrf_token()}}',
                message:$("form #message").val(),
            }
        }).done(function(res){
            $(".message > .message").last().after(res);
            $("form #message").val(' ');
            $(document).scrollTop($(document).height())
        });
    });
    </script>
</body>
</html>
