import React from "react";
import LoadingScreen from "./loading";
import EntityField from "./entityField";

export default class EntityFieldGroups extends React.Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    if (this.props.sections) {
      return this.props.sections.map((section) => {
        return (
          <table
            className="fancy-table optimizedForSideDetail"
            key={section.htmlId}
          >
            <thead>
              <tr>
                <th style={{ borderRadius: "16px 0 0 0" }}>
                  <span>{section.name} </span>
                  <span
                    style={{
                      float: "right",
                      color: "#0BA5DD",
                      cursor: "pointer",
                    }}
                  >
                    Unlock Editing
                  </span>
                </th>
              </tr>
            </thead>
            <tbody>

            <tr>
              <td>
                <table className="entity-fields-table">
                  <tbody>
                  {section.fields.map((fieldRow) => {
                    return (
                        <tr key={fieldRow.map(x=> x.htmlId).join('')}>
                          {fieldRow.map((field) => {
                            return <td key={field.htmlId}>
                              <EntityField field={field} />
                            </td>;
                          })}
                        </tr>
                    );
                  })}
                  </tbody>
                </table>
                </td>
              </tr>

            </tbody>
          </table>
        );
      });
    } else {
      // loading screen
      return <LoadingScreen />;
    }
  }
}
