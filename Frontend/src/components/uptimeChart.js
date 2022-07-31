/**
 * Renders an uptime chart.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import {Line} from "react-chartjs-2";
import $ from 'jquery';
import {
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  LineElement,
  PointElement,
  Title,
  Tooltip,
} from "chart.js";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

const options = {
    responsive: true,
    plugins: {
        legend: {
            position: "top",
        },
        title: {
            display: false,
        },
    },
    scales: {y: {max: 105, min: 0, ticks: {beginAtZero: true}}},
};

const loadingChart = {
    data: {
        labels: [...Array(10).keys()].map((x) => "Loading..."),
        datasets: [
            {
                label: "Uptime",
                data: [0],
                borderColor: "grey",
                backgroundColor: "grey",
            },
        ],
    },
};

export default function UptimeChart({entityId}) {
    const [chartType, setChartType] = useState('Hourly')
    const [chart, setChart] = useState(loadingChart.data)

    function getUptime() {
        setChart(loadingChart.data)
        $.get(`/SAT_BRH/API/entities/${entityId}/uptime/${chartType}`, function (response) {
            setChart(response.data)
        })
    }

    useEffect(() => {
        getUptime();
    }, [chartType])

    function toggleSelectedChartType() {
        setChartType(chartType === 'Hourly' ? 'Daily' : 'Hourly')
    }

    return (
        <div>
            <h5>
                <span>Uptime</span>
                <a
                    className="link"
                    style={{float: "right"}}
                    onClick={toggleSelectedChartType}
                >
                    {chartType}
                </a>
            </h5>

            <Line
                options={options}
                data={chart}
                width={"656px"}
                height={"328px"}
                style={{
                    display: "block",
                    boxSizing: "border-box",
                    height: "164px",
                    width: "328px",
                }}
                type={'line'}
            />
        </div>
    );
}
