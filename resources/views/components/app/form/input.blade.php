@php
    $options = ($options) ?? [];
    $optionsobj = ($optionsobj) ?? (object) [];
@endphp
@if( $type == "hidden" )
    @php
        $encrypt = $encrypt ?? true;
    @endphp
    @if( isset($value) )
        @php
            $value = ($encrypt) ? codeEncrypt($value) : $value;
        @endphp
        <input type="hidden" id="{{@$id}}" name="{{$name}}" value="{{ old($name, $value ) }}">

    @else
        <input type="hidden" id="{{@$id}}" name="{{$name}}">
    @endif
@else

    <div class="{{ classFormDiv( $size ) }}">
        
        @if( $type == "text" )
        
            <label>{{$label}}:</label>

            <input id="{{@$id}}" name="{{$name}}" value="{{ old($name, $value) }}" class="{{classForm()}}" @disabled(@$disabled) @readonly(@$readonly) onblur="{{@$onblur}}" @required(@$required) style="background-color: {{@$color}};">

        @elseif( $type == "file" )
            
            <label>{{$label}}:</label>
            <input type="file" name="{{$name}}">
        
        @elseif( $type == "select" )
            @php
                $options = ($name=="status_id") && (!$options) ? [1=>"Ativo", 0=>"Pendente", -1=>"Inativo"] : $options;
            @endphp

            @if( isset($label) )
                <label>{{$label}}:</label>
            @endif

            <select name="{{$name}}" id="{{@$id}}" class="{{classForm()}}" onchange="{{@$onchange}}" @disabled(@$disabled) @readonly(@$readonly) style="background-color: {{@$color}};" @required(@$required)>
                <option value="">--Selecione--</option>
                @forelse( $options as $_key => $_value )
                    <option value="{{$_key}}" @selected($value!="" && $_key==$value)>{{$_value}}</option>
                @empty
                @endforelse
                
                @forelse( $optionsobj as $_value )
                    <option value="{{$_value->id}}" @selected($_value->id==$value)>{{$_value->name}}</option>
                @empty
                @endforelse
            </select>

        @elseif( $type == 'multiselect') 
            <label>{{$label}}:</label>   

            <select class="{{classForm()}} col multipleSelect fstElement" multiple name="{{$name}}" id="{{@$id}}" onchange="{{@$onchange}}" @required(@$required) hidden="hidden">
                @forelse( @$options as $_key => $_value)
                    <option value="{{$_key}}" @selected(in_array($_key, $value ?? []))>{{$_value}}</option>
                @empty
                @endforelse

                @forelse( $optionsobj as $_value)
                    <option value="{{$_value->id}}" @selected(in_array($_value->id, $value ?? []))>{{$_value->name}}</option>
                @empty
                @endforelse
            </select>

        @elseif( $type == 'switch')
            <label class="form-check-label">{{$label}}</label><br>
            <div class="form-switch">
                <input class="{{classForm()}} form-check-input form-switch-small" type="checkbox" name="{{$name}}" value="1" role="switch" @checked(@$checked) onchange="{{@$onchange}}">
            </div>


        @elseif( $type == 'date')
            <label>{{$label}}</label>
            <input type="date" name="{{$name}}" class="{{classForm()}}" id="{{$name}}" placeholder="{{$label}}" value={{$value}} onchange="{{@$onchange}}">


        @elseif( $type == 'time')
            <label>{{$label}}</label>
            <input type="time" name="{{$name}}" class="{{classForm()}}" id="{{$name}}" placeholder="{{$label}}" value={{$value}} onchange="{{@$onchange}}"  @disabled(@$disabled)>


        @endif

    </div>

@endif

