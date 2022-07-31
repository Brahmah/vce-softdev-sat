/**
 * This is a component that fetches and displays activity items
 * The fetched items are dependant on the context and itemId as documented
 * in the API documentation.
 *
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React from "react";
import $ from "jquery";
import {useEffect} from "react";

// MARK: - Activity List
export function ActivityList({context, itemId}) {
    const [activity, setActivity] = React.useState([]);

    function getAreaActivity() {
        setActivity([]);
        $.get(`/SAT_BRH/API/activity/${context}/${itemId}`, function (data) {
            setActivity(data.activity);
        });
    }

    useEffect(() => {
        getAreaActivity();
    }, [itemId, context]);

    if (!activity || activity.length === 0) {
        return <div>No activity</div>;
    } else {
        return (
            <div className="activity-list">
                <table className="table table-striped">
                    <tbody>
                    {activity.map((item) => {
                        return (
                            <tr key={item.id}>
                                <td>
                                    <ActivityItemBodyBuilder body_builder={item.body_builder}/>
                                    <p className={'meta'}>{item.relative_date}</p>
                                </td>
                            </tr>
                        )
                    })}
                    </tbody>
                </table>
            </div>
        );
    }
}

// MARK: - Body Builder
export function ActivityItemBodyBuilder({body_builder}) {
    if (!body_builder || body_builder.length === 0) {
        return <p/>;
    } else {
        return (
            <p className="activity-item-body-built description">
                {body_builder.map((item) => {
                    if (item.type === 'param' && item?.attr?.href) {
                        return (
                            <span key={item.id}>
                                <a className={'link'} href={item.attr.href} style={{fontWeight: 'bold'}}>
                                    {item.text + ' '}
                                </a>
                            </span>
                        );
                    } else if (item.type === 'param' && item?.attr?.color) {
                        return (
                            <span key={item.id} style={{color: item.attr.color, fontWeight: 'bold'}}>
                                {item.text + ' '}
                            </span>
                        );
                    } else {
                        return <span key={item.id}>{item.text + ' '}</span>;
                    }
                })}
            </p>
        );
    }
}
