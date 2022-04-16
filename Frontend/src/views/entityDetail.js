import React, { useEffect, useState } from "react";
import EntitySideDetail from "../components/entitySideDetail";
import { useParams } from "react-router-dom";
import EntityFieldGroups from "../components/entityFieldGroups";

export default function EntityView() {
  const [error, setError] = useState(null);
  const [isLoaded, setIsLoaded] = useState(false);
  const [entity, setEntity] = useState([]);

  let params = useParams();
  let entityId = params.id;

  // Note: the empty deps array [] means
  // this useEffect will run once
  // similar to componentDidMount()
  useEffect(() => {
    fetch(`http://localhost/VSV/SAT/API/getEntity.php?id=${entityId}`)
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
  }, [entityId]);

  return (
    <div>
      {/*Header Bar */}
      <div className="areas-header">
        <span className="header networkingDeviceList">
          <span>
            <span>{entity.ip_address}</span>
            <span className="header-badge">{isLoaded ? entity.type : 'Loading...'}</span>
          </span>
        </span>
      </div>
      {/*Main Content*/}
      <div className="table-wrapper">
          {/*Main Detail */}
          <EntityFieldGroups sections={entity.sections} />
          {error && <div className="error">Error: {error.message}</div>}
          {/*Side Detail */}
        <EntitySideDetail entityId={entityId} />
      </div>
    </div>
  );
}
