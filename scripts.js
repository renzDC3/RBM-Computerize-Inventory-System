// Fetch data from the PHP script
fetch('top_products.php') // Update with the correct path
    .then(response => response.json())
    .then(data => {
        const productNames = data.map(product => product.product_name);
        const quantities = data.map(product => product.total_quantity);

        // Update bar chart options with fetched data
        const barChartOptions = {
            series: [
                {
                    data: quantities,
                    name: 'Products',
                },
            ],
            chart: {
                type: 'bar',
                background: 'transparent',
                height: 350,
                toolbar: {
                    show: false,
                },
            },
            colors: ['#FF0000', '#2962ff', '#583cb3', '#ff6d00', '#2e7d32'],
            plotOptions: {
                bar: {
                    distributed: true,
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '40%',
                },
            },
            dataLabels: {
                enabled: false,
            },
            fill: {
                opacity: 1,
            },
            grid: {
                borderColor: '#55596e',
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
                xaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
            legend: {
                labels: {
                    colors: 'black',
                },
                show: true,
                position: 'top',
            },
            stroke: {
                colors: ['transparent'],
                show: true,
                width: 2,
            },
            tooltip: {
                shared: false,
                intersect: false,
                theme: 'dark',
            },
            xaxis: {
                categories: productNames,
                title: {
                    style: {
                        color: 'black',
                    },
                },
                axisBorder: {
                    show: true,
                    color: '#55596e',
                },
                axisTicks: {
                    show: true,
                    color: '#55596e',
                },
                labels: {
                    style: {
                        colors: 'black',
                    },
                },
            },
            yaxis: {
                title: {
                    text: 'Count',
                    style: {
                        color: 'black',
                    },
                },
                axisBorder: {
                    color: '#55596e',
                    show: true,
                },
                axisTicks: {
                    color: '#55596e',
                    show: true,
                },
                labels: {
                    style: {
                        colors: 'black',
                    },
                },
            },
        };

        const barChart = new ApexCharts(
            document.querySelector('#bar-chart'),
            barChartOptions
        );
        barChart.render();
    })
    .catch(error => console.error('Error fetching data:', error));
