import React from "react";
import { useNavigate } from "react-router-dom";
import $ from "jquery";

export default class AreasView extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      error: null,
      isLoaded: false,
      areas: [],
    };
  }

  componentDidMount() {
    fetch("http://localhost/VSV/SAT/API/areas.php")
      .then((res) => res.json())
      .then(
        (result) => {
          this.setState({
            isLoaded: true,
            areas: result.list,
          });
        },
        // Note: it's important to handle errors here
        // instead of a catch() block so that we don't swallow
        // exceptions from actual bugs in components.
        (error) => {
          this.setState({
            isLoaded: true,
            error: "Error loading areas",
          });
        }
      );
  }

  render() {
    return (
      <div>
        {/*Header Bar With Search*/}
        <div className="areas-header">
          <span className="header networkingDeviceList">
            <span>
              <span>Areas</span>
              <span className="header-badge">38</span>
            </span>
            <input
              type="text"
              className="devicesSearch"
              style={{marginRight: "140px"}}
              placeholder="Search Areas"
              data-bind="textInput: deviceSearchQuery, event:{ change: $root.filterNetworkingDevices}"
            />
              <AddAreaButton/>
          </span>
        </div>
        {/*Area Cards */}
        <div className="mdc-layout-grid cards-areas-grid">
          <div className="mdc-layout-grid__inner">
            {this.state.areas.map((area) => (
              <AreaCard key={area.id} area={area} />
            ))}
          </div>
        </div>
      </div>
    );
  }
}

function AreaCard(props) {
  const navigate = useNavigate();
  const area = props.area;

  function handleClick() {
    navigate("/areas/" + area.id);
  }

  return (
      <div className="mdc-layout-grid__cell mdc-layout-grid__cell--span-4" style={{textDecoration: 'none'}} onClick={handleClick}>
          <div className="mdc-card mdc-card__primary-action">
              <div className="mdc-card__primary-action" tabIndex="0">
                  <div className="mdc-card__media mdc-card__media--square"
                       style={{backgroundColor: area.backgroundColor}}>
                  </div>
              </div>
              <div className="mdc-card__supporting-text">
                  <h2 className="mdc-card__title mdc-card__title--large">{area.name}</h2>
                  <h3 className="mdc-card__subtitle" style={{color: '#9e9e9e'}}>{area.description}</h3>
              </div>
          </div>
      </div>
  );
}

function AddAreaButton(props) {
    // isModalOpen state
    const [isModalOpen, setIsModalOpen] = React.useState(false);
    function toggleModal() {
        setIsModalOpen(!isModalOpen)
    }
    // area name state
    const [areaName, setAreaName] = React.useState("");
    function handleAreaNameChange(event) {
        setAreaName(event.target.value);
    }
    // area description state
    const [areaDescription, setAreaDescription] = React.useState("");
    function handleAreaDescriptionChange(event) {
        setAreaDescription(event.target.value);
    }
    // add area click handler
    function handleAddAreaClick() {
        $.ajax({
            url: "http://localhost/VSV/SAT/API/areas.php",
            type: "POST",
            data: {
                name: areaName,
                description: areaDescription
            },
            success: function (data) {
                console.log(data);
                // close modal
                setIsModalOpen(false);
                // refresh page
                window.location.reload();
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    return (
        <div>
            <button className="topBarActionButton" onClick={toggleModal}>
                <span className="mdc-fab__icon material-icons">add</span>
                <span className="mdc-fab__label">Add Area</span>
            </button>
            <div className={'topBarActionPopoverArrowUp'} style={{visibility: isModalOpen ? 'visible' : 'hidden'}}/>
            <div className="topBarActionPopover" style={{visibility: isModalOpen ? 'visible' : 'hidden'}}>
                <h3>Add Area</h3>
                <input type='text' placeholder={'Name'} disabled={true}/>
                <input type='text' className={'propertyInput'} placeholder={'Front Office'} value={areaName} onChange={handleAreaNameChange}/>
                <input type='text' placeholder={'Description'} disabled={true}/>
                <input type='text' className={'propertyInput'} placeholder={'D2.1 | Glenroy Campus'} value={areaDescription} onChange={handleAreaDescriptionChange}/>
                <button className={'actionButton'} onClick={handleAddAreaClick}>Add</button>
            </div>
        </div>
    );
}
