@if($logged_in_user->hasRole(['administrator']) || $logged_in_user->can('View dashboard Chart'))
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="agentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
