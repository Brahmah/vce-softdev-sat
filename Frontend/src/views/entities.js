/**
 * This renders the devices tab
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import {useNavigate} from "react-router-dom";
import { CSVLink } from "react-csv";
import $ from "jquery";

export default function EntitiesView() {
    const [allEntities, setAllEntities] = useState([])
    const [entities, setEntities] = useState([])
    const [areasCount, setAreasCount] = useState(0)
    const [searchQuery, setSearchQuery] = useState('')

    function reloadEntities() {
        $.get('/SAT_BRH/API/entities', (response) => {
            if (response?.count) {
                setAllEntities(response.entities.map(entity => {
                    return {
                        ...entity,
                        searchText: Object.keys(entity).map(key => entity[key]).join(' ').toLowerCase()
                    }
                }))
                setEntities(response.entities);
                setAreasCount(response.areasCount)
            }
        })
    }

    function handleSearchQueryChange(query) {
        query = query.toLowerCase()
        setSearchQuery(query)
        if (query && query.length > 0) {
            const filteredEntities = allEntities.filter(entity => {
                return entity.searchText.includes(query)
            })
            setEntities(filteredEntities)
        } else {
            setEntities(allEntities)
        }
        // update areas count
        setAreasCount([...new Set(entities.map(entity => entity.area.id))].length)
    }

    useEffect(() => {
        reloadEntities()
    }, [])

    return (
        <div>
            {/* Header Bar With Search */}
            <div className="areas-header">
                  <span className="header networkingDeviceList">
                    <span>
                      <span>Devices</span>
                      <span className="header-badge">{entities.length + ' devices in ' + areasCount + ' areas'}</span>
                        <CSVLink
                            data={entities}
                            filename={'devices.csv'}
                            headers={[
                                { label: 'ID', key: 'id' },
                                { label: 'Type', key: 'type_label' },
                                { label: 'Status', key: 'status' },
                                { label: 'IP Address', key: 'ip_address' },
                                { label: 'Name', key: 'name' },
                                { label: 'Area', key: 'area.label' },
                                { label: 'Notes', key: 'brief_notes' },
                                { label: 'MAC Address', key: 'mac_address' },
                                { label: 'Serial Number', key: 'serial_number' },
                                { label: 'Model', key: 'model' },
                                { label: 'Manufacturer', key: 'manufacturer' },
                                { label: 'Connection Type', key: 'connection_type' },
                            ]}
                            className="btn csv"
                        >
                          Download CSV
                        </CSVLink>
                    </span>
                    <input
                        type="text"
                        className="devicesSearch"
                        placeholder="Search"
                        style={{marginRight: 16}}
                        value={searchQuery}
                        onChange={(e) => {handleSearchQueryChange(e.target.value)}}
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
                        <th>Type</th>
                        <th>Area</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                    </thead>
                    <tbody>
                    {entities.map((entity) => (
                        <EntityRow entity={entity} key={entity.id}/>
                    ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

function EntityRow(props) {
    const navigate = useNavigate();
    const entity = props.entity;

    function handleClick() {
        navigate("/SAT_BRH/devices/" + entity.id);
    }

    return (
        <tr onClick={handleClick}>
            <td {...{"td-online-badge": "somevalue"}} >{entity.ip_address}</td>
            <td>{entity.name}</td>
            <td>{entity.type_label}</td>
            <td>{entity?.area?.label ?? 'Unassigned'}</td>
            <td>{entity.status}</td>
            <td>{entity.brief_notes}</td>
        </tr>
    );
}
