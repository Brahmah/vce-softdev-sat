import React from "react";
import { Line } from "react-chartjs-2";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
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
  scales: { y: { max: 105, min: 0, ticks: { beginAtZero: true } } },
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

export default class UptimeChart extends React.Component {
  constructor(props) {
    super(props);
    this.toggleSelectedChartType = this.toggleSelectedChartType.bind(this); // https://stackoverflow.com/a/32317459
    this.state = {
      chartType: "Hourly",
      uptimeChart: loadingChart,
    };
  }

  componentDidMount() {
    this.getUptime();
  }

  getUptime() {
    this.setState({
      uptimeChart: loadingChart,
    });
    fetch(
      `http://localhost/VSV/SAT/API/getEntityUptime.php?id=${this.props.entityId}&type=${this.state.chartType}`
    )
      .then((res) => res.json())
      .then((result) => {
        this.setState({
          uptimeChart: {
            data: result.data,
          },
        });
      });
  }

  toggleSelectedChartType() {
    if (this.state.chartType === "Hourly") {
      this.setState({
        chartType: "Daily",
      });
    } else {
      this.setState({
        chartType: "Hourly",
      });
    }
    this.getUptime();
  }

  render() {
    return (
      <div>
        <h5>
          <span>Uptime</span>
          <a
            className="link"
            style={{ float: "right" }}
            onClick={this.toggleSelectedChartType}
          >
            {this.state.chartType}
          </a>
        </h5>

        <Line
          options={options}
          data={this.state.uptimeChart.data}
          width={"656px"}
          height={"328px"}
          style={{
            display: "block",
            boxSizing: "border-box",
            height: "164px",
            width: "328px",
          }}
        />
      </div>
    );
  }
}
