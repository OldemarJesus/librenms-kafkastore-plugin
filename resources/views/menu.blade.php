@foreach($images as $image)
    <a href="{{ route('example-plugin.image', ['image' => $image]) }}">
        <i class="fa fa-image fa-fw fa-lg" aria-hidden="true"></i>
        {{ $image }}
    </a>
@endforeach
