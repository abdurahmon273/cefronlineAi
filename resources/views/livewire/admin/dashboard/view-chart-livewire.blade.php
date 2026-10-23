

<div class="row pt-5">


<div class="col-6">
    <canvas id="admin-line-chart"></canvas>
</div>


</div>


@script

<script>
    document.addEventListener('ReloadChart', function (event) {
        // Get the data from the event detail
        const chartMonthArray = event.detail['0']['0'];
        const chartBenefitData = event.detail['0']['1'];
        const daysInMonth = [];
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth(); // 0-based, so January is 0, February is 1, etc.

        // Get the number of days in the current month
        const days = new Date(year, month + 1, 0).getDate();

        // Generate an array of days
        for (let day = 1; day <= days; day++) {
            daysInMonth.push(day.toString()); // Convert to string if needed
        }

        const ctx = document.getElementById('admin-line-chart');

        // Set the width of the canvas before creating the chart
        ctx.width = 1000;  // Set desired width
        ctx.height = 500;  // Set desired height

        // Destroy the previous chart instance if it exists
        if (window.myLineChart) {
            window.myLineChart.destroy();
        }

        // Create the chart
        window.myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartMonthArray,
                datasets: [{
                    label: 'Oylik Ko`rishlar soni ',
                    data: chartBenefitData,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.5,
                }]
            },
        });
    });
    setTimeout(function (){
        ctx.width = 1000;  // Set desired width
        ctx.height = 500;  // Set desired height
    },2000)
</script>

@endscript
