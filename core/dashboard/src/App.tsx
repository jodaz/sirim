import React from 'react';
import { Admin, Resource } from 'react-admin';
import dataProvider from './dataProvider';
import { history } from './utils';
import { Dashboard } from './dashboard';
import concepts from './concepts';
import movements from './movements';
import liquidations from './liquidations';
import payments from './payments';
import cancellations from './cancellations';
import customRoutes from './routes';
import affidavits from './affidavits';
import taxpayers from './taxpayers';
import fines from './fines';

function App() {
  return (
    <Admin
      dashboard={Dashboard}
      history={history}
      customRoutes={customRoutes}
      dataProvider={dataProvider}
    >
      <Resource {...taxpayers} />
      <Resource {...payments} />
      <Resource {...cancellations} />
      <Resource {...liquidations} />
      <Resource {...concepts} />
      <Resource {...movements} />
      <Resource {...fines} />
      <Resource {...affidavits} />
      <Resource name="cancellation-types" />
      <Resource name="liquidation-types" />
      <Resource name="payment-types" />
      <Resource name="payment-methods" />
      <Resource name="taxpayer-types" />
      <Resource name="taxpayer-classifications" />
    </Admin>
  );
}

export default App;
