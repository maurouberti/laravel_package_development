<h1>{{ trans('Page::pages.title') }}</h1>

<ul>
    @foreach($Pages as $Page)
        <li>{{ $Page->title }}</li>
    @endforeach
</ul>

<a href="locale/en">en</a>
<br>
<a href="locale/pt-br">pt-br</a>