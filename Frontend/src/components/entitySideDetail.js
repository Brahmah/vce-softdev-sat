/**
 * This component renders the right-side entity uptime chart along with the activity items
 * relevant to the passed though entity id.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
import React, {useEffect} from "react";
import UptimeChart from "./uptimeChart";
import {ActivityItemBodyBuilder} from "./activityList";
import $ from "jquery";

export default function EntitySideDetail({entityId}) {
    const [activity, setActivity] = React.useState([]);
    const [isLoading, setIsLoading] = React.useState(true);

    function reloadActivity() {
        setIsLoading(true);
        $.get(`/SAT_BRH/API/activity/entity/${entityId}`, function (data, status) {
            setIsLoading(false);
            if (status === 'success') {
                setActivity(data.activity);
            }
        });
    }

    useEffect(() => {
        reloadActivity();
    }, [entityId])

    return (
        <div className="detail-pane side-detail">
            <UptimeChart entityId={entityId}/>

            <h5>Activity</h5>

            {isLoading ? (
                <div>Loading...</div>
            ) : (
                <div>
                    {activity.map((item, index) => (
                        <div
                            className={"inbox-task " + (item.is_recent ? "recentFeed" : "")}
                            key={item.id}
                        >
                            <div className="forms">
                                <h5>
                                    <span>{item.relative_date}</span>
                                </h5>
                                <h4>
                                    <span>
                                        <ActivityItemBodyBuilder body_builder={item.body_builder} />
                                    </span>
                                </h4>
                                <h5>
                                    <span>{String(item.context).toUpperCase()}</span>
                                </h5>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
