import React from 'react';
import { Admin, Resource } from 'react-admin';
import dataProvider from './dataProvider';
import { history } from './utils';
import { Dashboard } from './dashboard';
import users from './users';
import concepts from './concepts';
import movements from './movements';
import liquidations from './liquidations';
import payments from './payments';
import customRoutes from './routes';

function App() {
  return (
    <Admin
      dashboard={Dashboard}
      history={history}
      customRoutes={customRoutes}
      dataProvider={dataProvider}
    > 
      <Resource name='users' {...users} />
      <Resource {...payments} />
      <Resource {...liquidations} />
      <Resource {...concepts} />
      <Resource {...movements} />
    </Admin>
  );
}

export default App;
