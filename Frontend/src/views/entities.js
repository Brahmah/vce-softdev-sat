import React from "react";
import { useNavigate } from "react-router-dom";

export default class EntitiesView extends React.Component {
  constructor(props) {
    super(props);
    let context = props.context;
    this.state = {
      error: null,
      isLoaded: false,
      entities: [],
    };
  }

  componentDidMount() {
    fetch("http://localhost/VSV/SAT/API/entities")
      .then((res) => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            entities: result.list,
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
      <div>
        {/* Header Bar With Search */}
        <div className="areas-header">
          <span className="header networkingDeviceList">
            <span>
              <span>Page Title </span>
              <span className="header-badge">4 Areas</span>
            </span>
            <input
              type="text"
              className="devicesSearch"
              placeholder="Search"
              data-bind="textInput: deviceSearchQuery, event:{ change: $root.filterNetworkingDevices}"
              style={{ marginRight: 16 }}
            />
          </span>
        </div>
        {/*Devices Table*/}
        <div className="table-wrapper">
          <table className="fancy-table">
            <thead>
              <tr>
                <th>IP</th>
                <th>Name</th>
                <th>LOCATION</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              {this.state.entities.map((entity) => (
                <EntityRow entity={entity} key={entity.DEVICE_ID} />
              ))}
            </tbody>
          </table>
        </div>
      </div>
    );
  }
}

function EntityRow(props) {
  const navigate = useNavigate();
  const entity = props.entity;

  function handleClick() {
    navigate("/devices/" + entity.DEVICE_ID);
  }

  return (
    <tr onClick={handleClick}>
      <td {...{ "td-online-badge": "somevalue" }} >{entity.IP_ADDRESS}</td>
      <td>{entity.NAME}</td>
      <td>{entity.LOCATION}</td>
      <td>{entity.NOTES}</td>
    </tr>
  );
}
