/**
 * This component allows the user to customize the device type and its
 * field definitions.
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useState, useEffect} from "react";
import {Link, useParams} from "react-router-dom";
import $ from "jquery";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function DeviceTypesSettingsView() {
    let params = useParams()
    let definitionId = params.id;
    const [deviceTypeSettings, setDeviceTypeSettings] = useState({
        entity_type_id: -1,
        type_label: "Loading..",
        type_created_timestamp: -1,
        type_definitions: []
    })

    function reloadDeviceTypeSettings() {
        $.get(`/SAT_BRH/API/settings/entityTypes/${definitionId}`, (response) => {
            if (response.entity_type_id) {
                setDeviceTypeSettings(response)
            } else if (response.message === 'Entity Type not found.') {
                window.location.href = '/SAT_BRH/settings/'
            }
        })
    }

    useEffect(() => {
        reloadDeviceTypeSettings();
    }, [definitionId])

    function addDefinition() {
        $.post(`/SAT_BRH/API/settings/entityTypes/${definitionId}`, {
            action: 'NEW',
            label: 'New definition',
            placeholder: 'Foo',
            section: 'New',
            type: 'text',
            max_length: 250
        }, (response) => {
            if (!response.success) {
                alert('Failed to delete: ' + response.message)
            } else {
                reloadDeviceTypeSettings();
                toast.success('Added Field', {
                    position: "top-right",
                    autoClose: 600,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
            }
        })
    }

    function deleteDeviceType() {
        if (window.confirm('Are you sure you want to delete this Device Type? This will REMOVE ALL associated fields and data.')) {
            $.ajax(`/SAT_BRH/API/settings/entityTypes/${deviceTypeSettings.entity_type_id}`, {
                type: "DELETE",
                success: function(response) {
                    reloadDeviceTypeSettings();
                },
                error: function(err) {
                    alert( err )
                }
            })
        }
    }

    return (
        <div>
            <ToastContainer limit={3}/>
            {/*Header Bar */}
            <div className="areas-header">
                <span className="header networkingDeviceList">
                  <span>
                    <span>{'Customize Device Type'}</span>
                    <span className="header-badge">{deviceTypeSettings.type_label}</span>
                    <span className="trash-action" title={'Delete Device Type'} onClick={deleteDeviceType}>🗑️</span>
                  </span>
                </span>
            </div>
            {/*Main Content*/}
            <section className="settings device-types">
                <div className="settings-container">
                    <div className="settings-body">
                        <div className="settings-body-container">
                            <div className="settings-body-container-header">
                                <h2>
                                    Fields
                                    <span>
                                        <button onClick={addDefinition} className={'section-action-button'}>➕️</button>
                                    </span>
                                </h2>
                                <p className={'meta'}>These fields will be available to edit for devices with the {deviceTypeSettings.type_label} type.</p>
                            </div>
                            <div className="settings-body-container-body">
                                <table>
                                    <thead>
                                        <tr>
                                            <th scope="col">Label</th>
                                            <th scope="col">Placeholder</th>
                                            <th scope="col">Section</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Max Length</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {deviceTypeSettings.type_definitions.map(definition => {
                                            return <DeviceDefinitionView definition={definition}/>
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    );
}

function DeviceDefinitionView({definition}) {
    const [label, setLabel] = useState(String(definition.label))
    const [placeholder, setPlaceholder] = useState(String(definition.placeholder))
    const [section, setSection] = useState(String(definition.section))
    const [type, setType] = useState(String(definition.type))
    const [maxLength, setMaxLength] = useState(String(definition.max_length))

    function handleChange(event, field) {
        const newValue = event.target.value;
        switch (field) {
            case 'label':
                setLabel(newValue)
                break
            case 'placeholder':
                setPlaceholder(newValue)
                break
            case 'section':
                setSection(newValue)
                break
            case 'type':
                setType(newValue)
                break
            case 'max_length':
                setMaxLength(newValue)
                break
            default:
                console.error('Error handling device type setting change, field out of scope.')
                break
        }
        // check if save timeouts exists
        if (!window.saveTimeouts) {
            window.saveTimeouts = {};
        }
        // clear save timeout as the user is editing
        const timeoutKey = 'settings_definition_' + definition.definition_id;
        if (window.saveTimeouts[timeoutKey]) window.clearTimeout(window.saveTimeouts[timeoutKey]);
        // Store the timeout id again
        window.saveTimeouts[timeoutKey] = window.setTimeout(() => {
            $.post(`/SAT_BRH/API/settings/entityTypes/${definition.definition_id}`, {
                newValue: newValue,
                field: field,
                action: 'UPDATE'
            }, (response) => {
                if (!response.success) {
                    alert('Failed to save: ' + response.message)
                } else {
                    toast.success('✅ Saved!', {
                        position: "top-right",
                        autoClose: 600,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    });
                }
            })
        }, 400);
    }

    const inputTypes = [
        {name: 'Text', value: 'text'},
        {name: 'Date', value: 'date'},
        {name: 'Date Time', value: 'datetime-local'},
        {name: 'Number', value: 'number'},
        {name: 'Area', value: 'area'},
    ];

    function deleteDefinition() {
        if (window.confirm("Are you sure you want to delete this definition?")) {
            $.post(`/SAT_BRH/API/settings/entityTypes/${definition.definition_id}`, {
                action: 'DELETE'
            }, (response) => {
                if (!response.success) {
                    alert('Failed to delete: ' + response.message)
                } else {
                    setTimeout(() => {
                        toast.success('Deleted', {
                            position: "top-right",
                            autoClose: 600,
                            hideProgressBar: false,
                            closeOnClick: true,
                            pauseOnHover: true,
                            draggable: true,
                            progress: undefined
                        });
                    }, 500)
                }
            })
        }
    }

    return (
        <tr key={definition.definition_id}>
            <td>
                <input type="text" placeholder={definition.label} value={label} onChange={(e) => handleChange(e, 'label')}/>
            </td>
            <td>
                <input type="text" placeholder={definition.placeholder} value={placeholder} onChange={(e) => handleChange(e, 'placeholder')}/>
            </td>
            <td>
                <input type="text" placeholder={definition.section} value={section} onChange={(e) => handleChange(e, 'section')}/>
            </td>
            <td>
                <select onChange={(e) => handleChange(e, 'type')}>
                    {inputTypes.map((inputType) => {
                        return <option selected={inputType.value === type} value={inputType.value}>{inputType.name}</option>
                    })}
                </select>
            </td>
            <td>
                <input type="number" placeholder={type === 'text' ? definition.max_length : 'N/A'} disabled={type !== 'text'} min="2" value={type === 'text' ? maxLength : ''} onChange={(e) => handleChange(e, 'max_length')}/>
            </td>
            <td><a href="" className={'link delete-link'} onClick={deleteDefinition}>Delete</a></td>
        </tr>
    )
}
