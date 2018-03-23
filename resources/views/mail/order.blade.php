@if (isset($order->yacht_id) && $order->yacht_id)
<p>
    <b>Заявка на яхту #{{ $order->yacht_id }}</b>
</p>
@endif

@if (isset($order->branch) && $order->branch)
<p>
    Представительство: {{ $order->branch }}
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
