@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    <div class="card" style="margin-top: 5px;">
        <div class="card-header">
            @include('backend.includes.dashboard.widgetOption')
        </div>
        <div class="card-body">
            @include('backend.includes.dashboard.widget')
        </div>
    </div>

    <div class="card">
        <div class="card-header">

            <div class="row">
                <div class="col">
                    @include('backend.includes.dashboard.chartOption')
                </div>
            </div>

        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    @include('backend.includes.dashboard.transactionChart')
                </div>
            </div>
        </div>
    </div>



@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script>
        var labelList = {!! json_encode($day) !!};
        var dataList = {!! json_encode($amounts) !!};
        var chart = '';

        var graphColors = [];
        var graphOutlines = [];
        var hoverColor = [];

        $(document).ready(function () {
            if ($('#dashCategSelect').length > 0) {
                getData($('.selectpicker').val());
            } else {
                getData(5);
            }
            getChartData();
        });

        $('#chartYearSelect').on('change', function () {
            chart ? chart.destroy() : null;
            getChartData();
        });

        $('#chartTypeSelect').on('change', function () {
            chart ? chart.destroy() : null;
            getChartData();
        });

        function getChartData() {
            var chartBody = $('#trnChartBody');
            var chartYear = $("#chartYearSelect").val();
            var chartType = $("#chartTypeSelect").val();
            var token = $('#token').val();
            chartBody.addClass('loader');
            $.ajax({
                url: "{{route('admin.dashboard.getChartData')}}",
                type: "POST",
                data: {
                    year: chartYear,
                    type: chartType,
                    _token: token
                },
                success: function (e) {
                    chartBody.removeClass('loader');
                    if (e) {
                        fillChart(e);
                    }
                },
                error: function (e) {
                    chartBody.removeClass('loader');
                    console.log(e);
                }
            });

        }

        function fillChart(data) {
            if ($('#myChart').length > 0) {
                var ctx = document.getElementById('myChart').getContext('2d');

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.label,
                        datasets: [
                            // {
                            //     label: "New recurring + Onetime Amount",
                            //     backgroundColor: "#e74c3c",
                            //     borderColor: "#c0392b",
                            //     data: data.main
                            // },
                            // {
                            //     label: "Recurring Amount",
                            //     backgroundColor: "#2980b9",
                            //     data: data.recurring
                            // },
                            {
                                label: "Total Amount",
                                backgroundColor: "#27ae60",
                                data: data.tot
                            }
                        ]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {

                                    const formatter = new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'LKR',
                                        minimumFractionDigits: 2
                                    });

                                    return formatter.format(tooltipItem.value);
                                }
                            }
                        }
                    }
                });
            }
        }

        $('#linkGenerateToggle').change(function () {
            var type = $(this).prop('checked');
            var isEnable = type ? "true" : "false";
            var token = $('#token').val();
            $.ajax({
                url: "{{route('admin.isGenerateLink')}}",
                type: "POST",
                data: {
                    isEnable: isEnable,
                    _token: token
                },
                success: function (e) {
                    const Toast = swal.mixin({
                        toast: true,
                        position: 'top-center',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    Toast.fire({
                        type: 'success',
                        title: 'success'
                    });

                },
                error: function (e) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        function getData($type) {

            // if ($('#insCountData').length > 0) {
            //     var intData = document.getElementById('insCountData');
            // }

            if ($('#totTrnCountData').length > 0) {
                var totTrnCountData = document.getElementById('totTrnCountData');
            }

            if ($('#totTrnAmountData').length > 0) {
                var totTrnAmountData = document.getElementById('totTrnAmountData');
            }

            if ($('#totPoints').length > 0) {
                var totPoints = document.getElementById('totPoints');
            }

            if ($('#toQR').length > 0) {
                var totQR = document.getElementById('toQR');
            }

            if ($('#toPaymentLink').length > 0) {
                var totPaymentLink = document.getElementById('toPaymentLink');
            }

            if ($('#toCash').length > 0) {
                var totCash = document.getElementById('toCash');
            }
            //
            // if ($('#pendCountData').length > 0) {
            //     var pendingTotal = document.getElementById('pendCountData');
            // }

            var token = $('#token').val();

            $.ajax({
                url: "{{route('admin.dashboard.getData')}}",
                type: "POST",
                data: {
                    _type: $type,
                    _token: token
                },
                success: function (e) {
                    //console.log(e);
                    // if (intData) {
                    //     intData.innerText = e.insu;
                    // }
                    if (totTrnCountData) {
                        totTrnCountData.innerText = e.trn;
                    }
                    if (totTrnAmountData) {
                        totTrnAmountData.innerText = e.trnTot;
                    }
                    if (totPoints) {
                        totPoints.innerText = (e.pointBalance === undefined ? '0.00' : e.pointBalance);
                    }
                    if (totQR) {
                        totQR.innerText = 'Rs. ' + (e.qr === undefined ? '0.00' : e.qr);
                    }
                    if (totPaymentLink) {
                        totPaymentLink.innerText = 'Rs. ' + (e.payment_link === undefined ? '0.00' : e.payment_link);
                    }
                    if (totCash) {
                        totCash.innerText = 'Rs. ' + (e.cash === undefined ? '0.00' : e.cash);
                    }
                    // if (pendingTotal) {
                    //     pendingTotal.innerText = e.pend;
                    // }
                },
                error: function (e) {
                    console.log('error', e);
                }
            });
        }

        $('#dashCategSelect').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
            getData($('.selectpicker').val());
        });


    </script>
@endpush
