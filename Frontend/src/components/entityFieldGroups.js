/**
 * This component renders a group of entity fields in a table.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React from "react";
import LoadingScreen from "./loading";
import EntityField from "./entityField";

export default function EntityFieldGroups(props) {
    if (props.sections) {
        return props.sections.map((section) => {
            return (
                <table
                    className="fancy-table optimizedForSideDetail"
                    key={section.htmlId}
                >
                    <thead>
                    <tr>
                        <th style={{borderRadius: "16px 0 0 0"}}>
                            <span>{section.name} </span>
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
                                        <tr key={fieldRow.map(x => x.htmlId).join('')}>
                                            {fieldRow.map((field) => {
                                                return <td key={field.htmlId}>
                                                    <EntityField field={field} onShouldRefresh={props.onShouldRefresh}/>
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
        return <LoadingScreen/>;
    }
}
