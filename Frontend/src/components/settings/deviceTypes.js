/**
 * This component allows the user to CRUD the device types.
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useState, useEffect} from "react";
import {Link, useParams} from "react-router-dom";
import $ from 'jquery';
import Modal from "react-modal";

export default function DeviceTypesSettingsView() {
    const [deviceTypes, setDeviceTypes] = useState([]);

    function reloadDeviceTypes() {
        $.get('/SAT_BRH/API/settings/entityTypes', (response) => {
            if (response?.count) {
                setDeviceTypes(response.entity_types)
            }
        })
    }

    useEffect(() => {
        reloadDeviceTypes();
    }, [])

    return (
        <div className="settings-body-container">
            <div className="settings-body-container-header">
                <h2>
                    Device Types
                    <span> <SettingsEntityTypesSectionHeader onNewAddedDeviceType={()=> {
                        reloadDeviceTypes()
                    }}/> </span>
                </h2>
                <p className={'meta'}>Configure and customize your devices settings depending on their type</p>
            </div>
            <div className="settings-body-container-body">
                <table>
                    <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Number of devices</th>
                        <th scope="col">Number of fields</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        {
                            deviceTypes.map(deviceType => {
                                return (
                                    <tr key={deviceType.entity_type_id}>
                                        <td>{deviceType.type_label}</td>
                                        <td>{deviceType.entity_count}</td>
                                        <td>{deviceType.definition_count}</td>
                                        <td>{deviceType.created_date_friendly}</td>
                                        <td>
                                            <Link className={'link'} to={'/SAT_BRH/settings/device_types/' + deviceType.entity_type_id}>Customize</Link>
                                        </td>
                                    </tr>
                                )
                            })
                        }
                    </tbody>
                </table>
            </div>
        </div>
    );
}

function SettingsEntityTypesSectionHeader({onNewAddedDeviceType}) {
    const [isCreateDeviceTypeModalOpen, setIsCreateDeviceTypeModalOpen] = useState(false)

    function openNewUserModal() {
        setIsCreateDeviceTypeModalOpen(true)
    }

    const [deviceTypeLabel, setDeviceTypeLabel] = useState('')

    function createDeviceType() {
        setIsCreateDeviceTypeModalOpen(false)
        $.post('/SAT_BRH/API/settings/entityTypes', {
            label: deviceTypeLabel
        }).done(function( data ) {
            if (onNewAddedDeviceType) {
                onNewAddedDeviceType()
            }
        });
    }

    function closeNewDeviceTypeModel() {
        setIsCreateDeviceTypeModalOpen(false)
    }

    return (
        <div>
            <button onClick={openNewUserModal} className={'section-action-button'}>➕️</button>
            <Modal
                isOpen={isCreateDeviceTypeModalOpen}
                contentLabel="New Device Type Modal"
                onRequestClose={() => setIsCreateDeviceTypeModalOpen(false)}
                style={{
                    content: {
                        height: '222px'
                    }
                }}
            >
                <div className="modalContent">
                    <h3>New Device Type</h3>
                    <input type='text' placeholder={'Device Type Label'} disabled={true}/>
                    <input type='text' className={'propertyInput'} placeholder={'Router, Camera, etc..'} value={deviceTypeLabel}
                           onChange={(e) => setDeviceTypeLabel(e.target.value)}/>
                    <button className={'actionButton trailing'} onClick={createDeviceType}>Create</button>
                    <button className={'actionButton leading'} onClick={closeNewDeviceTypeModel}>Cancel</button>
                </div>
            </Modal>
        </div>
    )
}
