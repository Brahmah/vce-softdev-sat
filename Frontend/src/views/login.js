/**
 * This renders the login view
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useState} from "react";
import $ from "jquery";

export default function LoginView() {
    const [error, setError] = useState(null);
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');

    function attemptLogin() {
        $.ajax({
            type: 'POST',
            url: '/SAT_BRH/API/authenticate',
            data: {
                username: username,
                password: password
            },
            success: function (data) {
                window.location.href = '/SAT_BRH/home';
            },
            error: function (data) {
                const response = JSON.parse(data.responseText);
                setError(response.message);
            }
        })
    }

    return (
        <div>
            {/*Main Content*/}
            <section className={'login-container'}>
                <h3>Login</h3>
                <input type={'text'} placeholder={'Username'} value={username} onChange={e => setUsername(e.target.value)}/>
                <input type={'password'} placeholder={'Password'} value={password}
                       onChange={e => setPassword(e.target.value)}/>
                {error && <div className={'error'}>{error}</div>}
                <button onClick={attemptLogin}>Login</button>
            </section>
        </div>
    );
}
