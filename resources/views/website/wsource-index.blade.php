<x-app-layout>

    <x-app.box>
        
        @include('website._tabs')

        <x-app.form.form id="wSourceEdit" action="{{route('website-source-store')}}" method="POST">
            <input type="hidden" name="website_id" value="{{codeEncrypt($websiteId)}}">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="rewrite" id="rewrite">
        
            <x-app.table :titles="['Source Id','Fonte','Fonte Status','Categoria','Post-Status-Padrão','Reescrever','Status']">
                @foreach( $wSources as $wSource)
                    @php
                        $rewriteIcon = (@$wSource->doc->rewrite==1) ? "minus" : "plus";
                        $rewriteCall = "handleConfig(".(($rewriteIcon=="plus")?"1":"0").",'".codeEncrypt($wSource->id)."')";
                    @endphp
                    <tr>
                        <td>{{$wSource->source_id}}</td>
                        <td>{{$wSource->Source->name}}</td>
                        <td>{{$wSource->Source->getStatus()}}</td>
                        <td>{{$wSource->Source->Category->name}}</td>
                        <td>{{@$wSource->doc->defaultPostStatus}}</td>
                        <td>
                            <x-app.icon type="{{$rewriteIcon}}" :onclick="$rewriteCall"></x-app.icon>
                        </td>
                        <td>{{$wSource->getStatus()}}</td>
                    </tr>
                @endForeach
            </x-app.table>

        </x-app.form.form>

    </x-app.box>

</x-app-layout>

{{-- ### SCRIPTS ### --}}
<script>
    
    function handleConfig( value, wSourceId )
    {
        $("#id").val(wSourceId);
        $("#action").val("config");
        
        $("#rewrite").val(value);

        $("#wSourceEdit").submit();
    }

</script>
