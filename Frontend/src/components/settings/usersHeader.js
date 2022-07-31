/**
 * This component contains the users header which is also used
 * to provision the creation of new uer accounts,
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useState} from "react";
import Modal from "react-modal";
import $ from 'jquery';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function SettingsUsersSectionHeader({onNewUserAdd}) {
    const [isNewUserModalOpen, setIsNewUserModalOpen] = useState(false)

    function openNewUserModal() {
        setIsNewUserModalOpen(true)
    }

    const [newUserName, setNewUserName] = useState('')
    const [newUserPassword, setNewUserPassword] = useState('')
    const [newUserRole, setNewUserRole] = useState('standard')

    function createUser() {
        setIsNewUserModalOpen(false)
        $.ajax({
            url: '/SAT_BRH/API/users',
            type: 'POST',
            data: {
                username: newUserName,
                password: newUserPassword,
                role: newUserRole
            },
            success: function(data) {
                toast.success('Added User: ' + newUserName, {
                    position: "top-right",
                    autoClose: 600,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
                if (onNewUserAdd) {
                    onNewUserAdd()
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error(xhr, textStatus, errorThrown)
                toast.error('Failed to add user: ' + newUserName, {
                    position: "top-right",
                    autoClose: 900,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })
            },
        })
    }

    function closeNewUserModal() {
        setIsNewUserModalOpen(false)
    }

    return (
        <div>
            <ToastContainer/>
            <button onClick={openNewUserModal} className={'section-action-button'}>➕️</button>
            <Modal
                isOpen={isNewUserModalOpen}
                contentLabel="New User Modal"
                onRequestClose={() => setIsNewUserModalOpen(false)}
                style={{
                    content: {
                        height: '500px'
                    }
                }}
            >
                <div className="modalContent">
                    <h3>New User</h3>
                    <input type='text' placeholder={'Name'} disabled={true}/>
                    <input type='text' className={'propertyInput'} placeholder={'Username'} value={newUserName}
                           onChange={(e) => setNewUserName(e.target.value)}/>
                    <input type='text' placeholder={'Password'} disabled={true}/>
                    <input type='password' className={'propertyInput'} placeholder={'Password'}
                           value={newUserPassword} onChange={(e) => setNewUserPassword(e.target.value)}/>
                    <input type='text' placeholder={'Role'} disabled={true}/>
                    <select className={'propertyInput'} onSelect={(e) => setNewUserRole(e.target.value)}>
                        <option value={'standard'}>Standard</option>
                        <option value={'superuser'}>Superuser</option>
                    </select>
                    <button className={'actionButton trailing'} onClick={createUser}>Create</button>
                    <button className={'actionButton leading'} onClick={closeNewUserModal}>Cancel
                    </button>
                </div>
            </Modal>
        </div>
    )
}
