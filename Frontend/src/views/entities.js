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
    fetch("/SAT_BRH/API/entities")
      .then((res) => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            entities: result.entities,
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
                <th>Area</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              {this.state.entities.map((entity) => (
                <EntityRow entity={entity} key={entity.id} />
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
    navigate("/SAT_BRH/devices/" + entity.id);
  }

  return (
    <tr onClick={handleClick}>
      <td {...{ "td-online-badge": "somevalue" }} >{entity.ip_address}</td>
      <td>{entity.name}</td>
      <td>{entity.area_id}</td>
      <td>{entity.brief_notes}</td>
    </tr>
  );
}
