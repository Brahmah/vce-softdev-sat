/**
 * This component allows the user to CRUD other users authorized to use the application.
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import $ from 'jquery';
import SettingsUsersSectionHeader from "./usersHeader";
import {toast} from "react-toastify";
import { Menu, MenuItem, MenuButton, SubMenu } from '@szhsin/react-menu';
import '@szhsin/react-menu/dist/core.css';

export default function UsersSettingsView() {
    const [users, setUsers] = useState([]);

    function reloadUsers() {
        $.get('/SAT_BRH/API/users', (response) => {
            if (response?.count) {
                setUsers(response.users)
            }
        })
    }

    useEffect(() => {
        reloadUsers();
    }, [])

    function deleteUser(id) {
        if (window.confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: `/SAT_BRH/API/users/${id}`,
                type: 'DELETE',
                success: function(data) {
                    toast.success(data.message, {
                        position: "top-right",
                        autoClose: 600,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    });
                    reloadUsers();
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
            })
        }
    }

    function resetUserPassword(id) {
        let newPassword = prompt("Please enter the new password.", "**********");
        if (window.confirm('Are you sure you want to reset this password?')) {
            $.ajax({
                url: `/SAT_BRH/API/users/${id}/passwordReset`,
                type: 'POST',
                data: {
                    newPassword: newPassword
                },
                success: function(data) {
                    toast.success(data.message, {
                        position: "top-right",
                        autoClose: 600,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    });
                    reloadUsers();
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
            })
        }
    }

    function updateUserRole(id, newRole) {
        if (window.confirm('Are you sure?')) {
            $.ajax({
                url: `/SAT_BRH/API/users/${id}/role`,
                type: 'POST',
                data: {
                    newRole: newRole
                },
                success: function(data) {
                    toast.success(data.message, {
                        position: "top-right",
                        autoClose: 600,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    });
                    reloadUsers();
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
            })
        }
    }

    return (
        <div className="settings-body-container">
            <div className="settings-body-container-header">
                <h2>
                    Login
                    <span>
                        <SettingsUsersSectionHeader onNewUserAdd={() => {
                            reloadUsers()
                        }}/>
                    </span>
                </h2>
                <p className={'meta'}>These user accounts have access to the system at varying levels.</p>
            </div>
            <div className="settings-body-container-body">
                <table>
                    <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Created Date</th>
                        <th scope="col">Created By</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {users.map(user => {
                        return (
                            <tr>
                                <td>{user.username}</td>
                                <td>{user.role}</td>
                                <td>{user.created.friendly}</td>
                                <td>{user.creator.username}</td>
                                <td>
                                    <Menu menuButton={<MenuButton className={'menu-button-action'}>⋮</MenuButton>}>
                                        <MenuItem className={'link delete-link'} onClick={() => {
                                            deleteUser(user.id)
                                        }}>Delete</MenuItem>
                                        <MenuItem className="link delete-link" onClick={() => {
                                            resetUserPassword(user.id)
                                        }} style={{
                                            color: '#e28a08'
                                        }}>Reset Password</MenuItem>
                                        <MenuItem className="link delete-link" onClick={() => {
                                            updateUserRole(user.id, user.role === 'superuser' ? 'standard' : 'superuser')
                                        }} style={{
                                            color: '#3f51b5'
                                        }}>{user.role === 'superuser' ? 'Demote to peasant' : 'Promote to superuser'}</MenuItem>
                                    </Menu>
                                </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
