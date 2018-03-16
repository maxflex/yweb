@if ($order->yacht_id)
<p>
    <b>Заявка на яхту #{{ $order->yacht_id }}</b>
</p>
@endif
<p>
    Имя: {{ $order->name }}
</p>
<p>
    Телефон: {{ $order->phone }}
</p>
<p>
    Комментарий: {{ $order->comment }}
</p>