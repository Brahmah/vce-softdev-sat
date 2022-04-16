import React from "react";
import $ from "jquery";

export default class EntityField extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    return (
      <table className="entity-field">
        <tbody>
          <tr>
            <td>
              <span id={"field-label-status-" + this.props.field.htmlId}>
                {this.props.field.label}
              </span>
            </td>
            <td style={{width: '1%'}}>
              {this.props.field.type === "select" ? (
                <EntityField_Select field={this.props.field} />
              ) : (
                <EntityField_Common field={this.props.field} />
              )}
            </td>
          </tr>
        </tbody>
      </table>
    );
  }
}

class EntityField_Select extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    return (
      <select
        id={this.props.field.htmlId}
        style={{
          marginLeft: "inherit",
        }}
      >
        <option value="f">Select</option>
        <option value="dd">Front Balcony</option>
        <option value="dd">Front Hdff</option>
      </select>
    );
  }
}

function EntityField_Common(props) {
  // autosave timing
  const [fieldText, setFieldText] = React.useState(props.field.value);
  // autosave response handling
  const [cssStatusResponse, setCssStatusResponse] = React.useState("");

  function handleChange(newValue) {
    setFieldText(newValue);
      // check if save timeouts exists
      if (!window.saveTimeouts) {
          window.saveTimeouts = {};
      }
      // clear save timeout as the user is editing
      if (window.saveTimeouts[props.field.parentEntityId]) window.clearTimeout(window.saveTimeouts[props.field.parentEntityId]);
      // Store the timeout id again
      window.saveTimeouts[props.field.parentEntityId] = window.setTimeout(() =>{
          console.log('Saving ' + props.field.parentEntityId);
          $.get(
              "http://localhost/VSV/SAT/API/saveEntityField.php",
              {
                  entityId: props.field.parentEntityId,
                  fieldId: props.field.htmlId,
                  value: fieldText,
              },
              (response) => {
                  setCssStatusResponse(response.cssStatus);
                  if (response.success === true) {
                      setTimeout(() => {
                          if (newValue !== props.field.value) {
                              setCssStatusResponse("");
                          }
                      }, 3000);
                  }
              }
          );
      }, 400);
  }

  return (
    <div>
      <input
        id={props.field.htmlId}
        type={props.field.type}
        value={fieldText}
        placeholder={props.field.placeholder}
        onChange={(e) => handleChange(e.target.value)}
      />
      <span dangerouslySetInnerHTML={{ __html: cssStatusResponse }} />
    </div>
  );
}
