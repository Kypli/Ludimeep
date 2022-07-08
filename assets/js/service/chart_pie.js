import '../../modules/chart/3.8.0.js';

export function pie(ctx, labels, datas){
	new Chart(ctx, {
		type: 'pie',
		data: {
			labels: labels,
			datasets: [{
				label: '# de votes',
				data: datas,
				backgroundColor: [
					'rgba(245, 28, 28, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(240, 240, 108, 0.2)',
					'rgba(64,224,208, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(245, 163, 28, 0.2)',
					'rgba(5, 122, 19, 0.2)',
					'rgba(255, 115, 251, 0.2)'
				],
				borderColor: [
					'rgba(245, 28, 28, 1)',
					'rgba(54, 162, 235, 1)',
					'rgba(238, 245, 28, 1)',
					'rgba(64,224,208, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(245, 163, 28, 1)',
					'rgba(5, 122, 19, 1)',
					'rgba(255, 115, 251, 1)'
				],
				borderWidth: 1
			}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: {
					position: 'top',
				},
				title: {
					display: false,
					// text: 'Chart.js Pie Chart'
				}
			}
		}
	});
}