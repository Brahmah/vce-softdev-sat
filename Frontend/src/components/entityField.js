/**
 * This component renders a contextual input/select field for a given entity field.
 * It will also handle the autosave functionality.
 *
 * This super dooper cool implementation is more thoroughly documented in the API documentation.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import $ from "jquery";

export default function EntityField(props) {
    return (
        <table className="entity-field">
            <tbody>
            <tr>
                <td>
                  <span id={"field-label-status-" + props.field.htmlId}>
                    {props.field.label}
                  </span>
                </td>
                <td style={{width: '1%'}}>
                    {(() => {
                        switch (props.field.type) {
                            case 'device_type':
                                return <EntityField_Select field={props.field} onShouldRefresh={props.onShouldRefresh}/>
                            case 'area':
                                return <EntityField_Select field={props.field} onShouldRefresh={props.onShouldRefresh}/>
                            default:
                                return <EntityField_Common field={props.field} onShouldRefresh={props.onShouldRefresh}/>
                        }
                    })()}
                </td>
            </tr>
            </tbody>
        </table>
    );
}

function EntityField_Select(props) {
    const [options, setOptions] = useState([
        {
            name: props.field.value,
            label: props.field.value,
            isDefault: true
        }
    ])
    const [isLoading, setIsLoading] = useState(true)

    function reloadOptions() {
        setIsLoading(true)
        $.get(`/SAT_BRH/API/entities/${props.field.parentEntityId}/fields/${props.field.htmlId}/options`, (response) => {
            setIsLoading(false)
            setOptions(response.options)
        })
    }

    useEffect(() => {
        reloadOptions()
    }, [props.field])

    const [cssStatusResponse, setCssStatusResponse] = React.useState("");

    function handleChange(newValue) {
        console.log('Saving ' + props.field.parentEntityId);
        $.post(
            `/SAT_BRH/API/entities/${props.field.parentEntityId}/fields/${props.field.htmlId}/save`,
            {
                value: newValue,
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
                if (props.onShouldRefresh) {
                    props.onShouldRefresh(props.field.type === 'area');
                }
            }
        );
    }

    return (
        <div>
            <select
                id={props.field.htmlId}
                style={{
                    marginLeft: "inherit",
                }}
                disabled={isLoading}
                onChange={(e) => handleChange(e.target.value)}
            >
                {
                    options.map(option => {
                        return <option value={option.value} label={option.label} selected={option.isDefault}/>
                    })
                }
            </select>
            <span dangerouslySetInnerHTML={{__html: cssStatusResponse}}/>
        </div>
    );
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
        window.saveTimeouts[props.field.parentEntityId] = window.setTimeout(() => {
            console.log('Saving ' + props.field.parentEntityId);
            $.post(
                `/SAT_BRH/API/entities/${props.field.parentEntityId}/fields/${props.field.htmlId}/save`,
                {
                    value: newValue,
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
                disabled={!props.field.isEditable}
                onChange={(e) => handleChange(e.target.value)}
            />
            <span dangerouslySetInnerHTML={{__html: cssStatusResponse}}/>
        </div>
    );
}
