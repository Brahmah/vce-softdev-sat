/**
 * This file houses components that are contextually related to the selected
 * area in the area tree. The area tree is the left side of the screen, and the
 * area detail is the right side of the screen.
 *
 * The area detail is able to refresh the area tree via a passed in completion callback.
 * This is used to refresh the area tree when the area detail is updated.
 *
 * The following example shows how to use the area detail:
 * 1. Create a new area detail component
 * 2. Pass in the area tree completion callback
 * 3. Render the area detail
 * 4. When the area detail is updated, call the area tree completion callback
 * 5. The area tree will refresh
 *
 * The user can use the area detail to add child areas to the tree and delete them.
 * These operations are usually prompt the refreshTree callback to be called.
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect} from "react";
import $ from "jquery";
import Modal from 'react-modal';
import {ActivityList} from "./activityList";
import EntityFieldGroups from "./entityFieldGroups";
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { Menu, MenuItem, MenuButton, SubMenu } from '@szhsin/react-menu';
import '@szhsin/react-menu/dist/core.css';

// MARK: - Areas Detail
export function AreasDetail({selectedItem, reloadAreaTree}) {
    if (selectedItem === null) {
        return <div>Select an area</div>;
    } else if (selectedItem.type === 'entity') {
        return <EntityItemDetail item={selectedItem} reloadAreaTree={reloadAreaTree}/>;
    } else {
        return <AreaItemDetail item={selectedItem} reloadAreaTree={reloadAreaTree}/>;
    }
}

// MARK: - Area Item Detail
function AreaItemDetail({item, reloadAreaTree}) {
    const defaultAreaInfo = {
        totalEntities: '...',
        onlineEntities: '...',
        offlineEntities: '...',
    };
    const [areaInfo, setAreaInfo] = React.useState(defaultAreaInfo);
    const [isEditModalOpen, setIsEditModalOpen] = React.useState(false);
    const [areaName, setAreaName] = React.useState(item.name);
    const [areaDescription, setAreaDescription] = React.useState(item.description);

    function getItemInfo() {
        setAreaInfo(defaultAreaInfo);
        $.get(`/SAT_BRH/API/areas/${item.id}/info`, function (data) {
            setAreaInfo(data);
        });
    }

    function closeEditModal() {
        setIsEditModalOpen(false);
    }

    function openEditModal() {
        setIsEditModalOpen(true);
    }

    function saveAreaChanges() {
        closeEditModal();
        $.ajax({
            url: `/SAT_BRH/API/areas/${item.id}`,
            type: 'POST',
            data: {
                name: areaName,
                description: areaDescription
            },
            success: function (data) {
                item.label = areaName;
                item.description = areaDescription;
                getItemInfo();
                reloadAreaTree();
            },
            error: function (data) {
                console.log(data); // TODO: Show error message
            }
        });
    }

    function manipulateArea(type, context) {
        $.ajax({
            url: `/SAT_BRH/API/areas/${item.id}/manipulate`,
            type: 'POST',
            data: {
                type: type,
                context: context,
                item_type: item?.type
            },
            success: function(data) {
                if (reloadAreaTree) {
                    reloadAreaTree();
                }
                toast.success(data.message, {
                    position: "top-right",
                    autoClose: 600,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error(xhr, textStatus, errorThrown)
                toast.error(JSON.parse(xhr.responseText).message, {
                    position: "top-right",
                    autoClose: 3200,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })
            },
        });
    }

    useEffect(() => {
        setAreaName(item.label);
        setAreaDescription(item.description);
        getItemInfo();
    }, [item]);

    return (
        <div style={{marginRight: '23px'}}>
            <h2>{item.label}</h2>
            <h3 style={{color: 'gray', fontSize: '14px', fontWeight: 200, marginTop: '-14px'}}>{item.description}</h3>
            <div>
                <Menu menuButton={<MenuButton className={'action-button'}>🔧</MenuButton>}>
                    <MenuItem className={'link'} onClick={() => {
                        openEditModal()
                    }}>Edit</MenuItem>
                    <MenuItem className="link delete-link" onClick={() => {
                        manipulateArea('add', 'entity')
                    }} style={{
                        color: '#3f51b5'
                    }}>{'Add Entity'}</MenuItem>
                    <MenuItem className="link delete-link" onClick={() => {
                        manipulateArea('add', 'area')
                    }} style={{
                        color: '#3f51b5'
                    }}>{'Add Area'}</MenuItem>
                    <MenuItem className="link delete-link" onClick={() => {
                        manipulateArea('delete', 'area')
                    }} style={{
                        color: 'red'
                    }}>{'Delete Area'}</MenuItem>
                </Menu>
                <Modal
                    isOpen={isEditModalOpen}
                    contentLabel="Area Edit Modal"
                >
                    <div className="modalContent">
                        <h3>Modify Area</h3>
                        <input type='text' placeholder={'Name'} disabled={true}/>
                        <input type='text' className={'propertyInput'} placeholder={item.label} value={areaName}
                               onChange={(e) => setAreaName(e.target.value)}/>
                        <input type='text' placeholder={'Description'} disabled={true}/>
                        <input type='text' className={'propertyInput'} placeholder={item.description}
                               value={areaDescription} onChange={(e) => setAreaDescription(e.target.value)}/>
                        <button className={'actionButton trailing'} onClick={saveAreaChanges}>Save</button>
                        <button className={'actionButton leading'} onClick={closeEditModal}>Cancel
                        </button>
                    </div>
                </Modal>
            </div>

            <div className="island">
                <h2 className="subheader">Summary</h2>

                <div className="content">
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{'color': '#008000'}}>Online Devices</span>
                            <span className="value">{areaInfo.onlineEntities}</span>
                        </p>
                    </div>
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{color: '#ff0000'}}>Offline Devices</span>
                            <span className="value">{areaInfo.offlineEntities}</span>
                        </p>
                    </div>
                    <div className="summary-item">
                        <p className="infographic">
                            <span className="title" style={{color: '#1b4a6b'}}>Total</span>
                            <span className="value">{areaInfo.totalEntities}</span>
                        </p>
                    </div>
                </div>

            </div>

            <div className="island">
                <h2 className="subheader" style={{opacity: 0.1}}>`</h2>
                <h2 className="subheader">Recent Activity</h2>

                <div className="content">
                    <ActivityList context={'area'} itemId={item.id}/>
                </div>
            </div>

        </div>
    );
}

// MARK: - Entity Item Detail
function EntityItemDetail({item, reloadAreaTree}) {
    const [entityInfo, setEntityInfo] = React.useState({});

    function getItemInfo() {
        $.get(`/SAT_BRH/API/entities/${item.id}`, function (data, status) {
            if (status === 'success') {
                setEntityInfo(data);
            } else {
                console.log(data); // TODO: Show error message
            }
        })
    }

    useEffect(() => {
        setEntityInfo({});
        getItemInfo();
    }, [item]);

    function onFieldGroupShouldRefresh(shouldRefreshPage) {
        getItemInfo();
        if (shouldRefreshPage) {
            reloadAreaTree();
        }
    }

    return (
        <div>
            <h2>{item.label}</h2>
            <a href={`/SAT_BRH/devices/${item.id}`} className={'action-button'}>📊</a>
            <div className="table-wrapper mini">
                {/*Main Detail */}
                <EntityFieldGroups sections={entityInfo.sections} onShouldRefresh={onFieldGroupShouldRefresh}/>
            </div>
            {/*<UptimeChart entityId={item.id} style={{width: '200px'}}/>*/}
        </div>
    );
}
