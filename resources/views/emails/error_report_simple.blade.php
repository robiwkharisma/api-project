<h2>Error on {!! date("Y/m/d h:i:sa") !!}</h2>
<hr/>

<h2>Request:</h2>
IP:
<pre>
{!! print_r($requestIP, TRUE) !!}
</pre>
URI:
<pre>
{!! print_r($requestURI, TRUE) !!}
</pre>
Request:
<pre>
{!! print_r($request, TRUE) !!}
</pre>
Header :
<pre>
{!! print_r($header, TRUE) !!}
</pre>
JSON Request:
<pre>
{!! print_r($requestJson, TRUE) !!}
</pre>
<hr/>
<h2>Exceptions:</h2>
<pre>
{!! print_r($exception, TRUE) !!}
</pre>
