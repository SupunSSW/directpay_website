<div class="row">
    <div class="col">
        <div class="float-left">
            <select class="selectpicker show-tick" data-style="btn-info" id="chartYearSelect">
                <option value="" >Select Year...</option>
                @foreach($years as $item)
                    @if($item)
                        @if($thisYear == $item)
                            <option value="{!! $item !!}" selected>{!! $item !!}</option>
                        @else
                            <option value="{!! $item !!}">{!! $item !!}</option>
                        @endif
                    @endif
                @endforeach
            </select>
        </div>
        <div class="float-right">
            <select class="selectpicker show-tick" data-style="btn-info" id="chartTypeSelect">
                <option value="" selected>Select Type...</option>
                <option value="month" selected>By Months</option>
                <option value="week">By Weeks</option>
            </select>
        </div>
    </div>
</div>


