import * as React from "react";
import {
  Filter,
  TextInput,
  List,
  Datagrid,
  NumberField,
  TextField,
  SimpleList,
  ReferenceArrayInput,
  SelectInput
} from 'react-admin';
import { Theme, useMediaQuery } from '@material-ui/core';

const FinesFilter: React.FC = props => (
  <Filter {...props}>
    <TextInput label="Número" source='num' />
    <TextInput label="Contribuyente" source='taxpayer' />
    <TextInput label="Monto" source='amount' />
    <ReferenceArrayInput
        source="concept_id"
        reference="concepts"
        label="Rubro"
        filter={{ 'liquidation_type_id': 2 }}
    >
      <SelectInput source="name" label="Tipo" allowEmpty={false} />
    </ReferenceArrayInput>
  </Filter>
);

const FinesList: React.FC = props => {
  const isSmall = useMediaQuery<Theme>(theme => theme.breakpoints.down('sm'));

  return (
    <List {...props}
      title="Sanciones"
      bulkActionButtons={false}
      filters={<FinesFilter />}
      exporter={false}
    >
      {
        isSmall
        ? (
          <SimpleList
            primaryText={record => `${record.num}`}
            secondaryText={record => `${record.object_payment}`}
            linkType={"show"}
          />
        )
        : (
          <Datagrid>
            <TextField source="num" label="Número"/>
            <TextField source="concept.name" label="Rubro"/>
            <NumberField source='amount' label='Monto' />
            <TextField source="taxpayer.name" label="Contribuyente"/>
          </Datagrid>
        )
      }
    </List>
  );
};

export default FinesList;