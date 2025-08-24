<div class="row">
    
    <x-app.form.input size="3" type="multiselect" label="Dias da Semana" name="doc[week_days][wd_1][days][]" :value="@$weekdaysvalue->days" :options="@$weekdays"></x-app.form.input>

    <x-app.form.input size="2" type="time" label="Abertura" name="doc[week_days][wd_1][opening]" :value="@$weekdaysvalue->opening"></x-app.form.input>
    
    <x-app.form.input size="2" type="time" label="Fechamento" name="doc[week_days][wd_1][closing]" :value="@$weekdaysvalue->closing"></x-app.form.input>
</div>