document.addEventListener("DOMContentLoaded", function () {
    {% for employee in employees %}
    const ctx = document.getElementById('chart-{{ employee.id }}').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Satisfaction', 'Dissatisfaction'],
            datasets: [{
                label: 'Performance Chart',
                data: [
                    {{ employee.satisfaction_count }},
                    {{ employee.dissatisfaction_count }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)', // Satisfaction color
                    'rgba(255, 99, 132, 0.6)'  // Dissatisfaction color
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
    {% endfor %}
});
