@if($hide_link)
    <span ng-init="groups={{ json_encode($urls) }}"></span>
@else
    <a ng-click='gallery.open()'>фотогалерея ({{ count(flatten($urls, 'photo')) }})</a>
@endif
<ng-image-gallery images="{{ json_encode(flatten($urls, 'photo')) }}" thumbnails='false' methods='gallery' bg-close='true'></ng-image-gallery>
