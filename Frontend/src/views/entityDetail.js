/**
 * This renders the entity detail view
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect, useState} from "react";
import EntitySideDetail from "../components/entitySideDetail";
import {useParams} from "react-router-dom";
import EntityFieldGroups from "../components/entityFieldGroups";
import $ from "jquery";

export default function EntityView() {
    const [error, setError] = useState(null);
    const [isLoaded, setIsLoaded] = useState(false);
    const [entity, setEntity] = useState([]);

    let params = useParams();
    let entityId = params.id;

    useEffect(() => {
        reloadEntity();
    }, [entityId]);

    function reloadEntity() {
        fetch(`/SAT_BRH/API/entities/${entityId}`)
            .then((res) => res.json())
            .then(
                (result) => {
                    setIsLoaded(true);
                    setEntity(result);
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    setIsLoaded(true);
                    setError(error);
                }
            );
    }

    function deleteEntity() {
        if (window.confirm('Are you sure you want to delete this Entity? This will REMOVE ALL associated data.')) {
            $.ajax(`/SAT_BRH/API/entities/${entityId}`, {
                type: "DELETE",
                success: function (data, status) {
                    if (status === 'success') {
                        window.history.back()
                    } else {
                        alert('Failed to delete: ' + data);
                    }
                }
            });
        }
    }

    return (
        <div>
            {/*Header Bar */}
            <div className="areas-header">
                <span className="header networkingDeviceList">
                  <span>
                      <span>{entity.ip_address}</span>
                      <span className="header-badge">{isLoaded ? entity.name : 'Loading...'}</span>
                      <span className="trash-action" title={'Delete Device Type'} onClick={deleteEntity}>🗑️</span>
                  </span>
                </span>
            </div>
            {/*Main Content*/}
            <div className="table-wrapper">
                <BreadcrumbsView entityId={entityId}/>
                {/*Main Detail */}
                <EntityFieldGroups sections={entity.sections} onShouldRefresh={reloadEntity}/>
                {error && <div className="error">Error: {error.message}</div>}
                {/*Side Detail */}
                <EntitySideDetail entityId={entityId}/>
            </div>
        </div>
    );
}

function BreadcrumbsView({entityId}) {
    const [isLoaded, setIsLoaded] = useState(false);
    const [crumbs, setCrumbs] = useState([]);
    const [error, setError] = useState(null);

    useEffect(() => {
        reloadCrumbs();
    }, [entityId]);

    function reloadCrumbs() {
        $.get(`/SAT_BRH/API/entities/${entityId}/breadcrumb`, function (data, status) {
            setIsLoaded(true);
            if (status === 'success') {
                setCrumbs(data.crumbs);
                setError(null)
            } else {
                setError(data);
            }
        });
    }

    if (!isLoaded) {
        return (
            <nav className="breadcrumbs">
                <a href="#" className="breadcrumbs__item is-active">Loading...</a>
            </nav>
        )
    } else if (error) {
        return (
            <nav className="breadcrumbs">
                <a href="#" className="breadcrumbs__item is-active">{JSON.stringify(error)}</a>
            </nav>
        )
    } else {
        return (
            <nav className="breadcrumbs">
                {
                    crumbs.map(crumb => {
                        return (
                            <a key={crumb.id} href={crumb.href} className={`breadcrumbs__item ${crumb.isActive ? ' is-active' : ''}`}>{crumb.label}</a>
                        )
                    })
                }
            </nav>
        )
    }
}

