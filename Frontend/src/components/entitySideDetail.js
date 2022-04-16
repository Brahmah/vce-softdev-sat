import React from "react";
import UptimeChart from "./uptimeChart";

export default class EntitySideDetail extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      activity: [],
    };
  }

  componentDidMount() {
    fetch(
      `http://localhost/VSV/SAT/API/getEntityActivity.php?entityId=${this.props.entityId}`
    )
      .then((res) => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            activity: result.list,
          });
        },
        // Note: it's important to handle errors here
        // instead of a catch() block so that we don't swallow
        // exceptions from actual bugs in components.
        (error) => {
          this.setState({
            isLoaded: true,
            error: "Error loading entities",
          });
        }
      );
  }

  render() {
    return (
      <div className="class-detail side-detail">
        <UptimeChart entityId={this.props.entityId} />

        <h5>Activity</h5>

        {this.state.isLoaded ? (
          <div>
            {this.state.activity.map((item, index) => (
              <div
                className={"inbox-task " + (item.isRecent ? "recentFeed" : "")}
                key={item.id}
              >
                <div className="forms">
                  <div className="min_foto">
                    <img
                      className="dwimg"
                      src={item.image}
                      alt={"profile picture"}
                    />
                  </div>
                  <h5>
                    <span>{item.title}</span>
                  </h5>
                  <h4>
                    <span>{item.description}</span>
                  </h4>
                  <h5>
                    <span>{item.relativeTime}</span>
                  </h5>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div>Loading...</div>
        )}
      </div>
    );
  }
}
