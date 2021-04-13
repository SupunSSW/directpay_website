<style>
    .loader{
        background: url('//upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Phi_fenomeni.gif/50px-Phi_fenomeni.gif')
        50% 50% no-repeat rgb(249,249,249);
    }
</style>

@if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard Chart'))
    <div class="row">
        <div class="col">
            <div class="card" id="trnChartBody">
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
