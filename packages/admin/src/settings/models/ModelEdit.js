import * as React from 'react'
import {
    useMutation,
    ReferenceInput,
    SelectInput,
    useEditController,
    useRedirect,
    useNotify
} from 'react-admin'
import { validateModel } from './modelValidations';
import BaseForm from '@sauco/lib/components/BaseForm'
import InputContainer from '@sauco/lib/components/InputContainer'
import { useParams } from 'react-router-dom'
import TextInput from '@sauco/lib/components/TextInput'

const ModelEdit = props => {
    const { id } = useParams();
    const editControllerProps = useEditController({
        ...props,
        id: id
    });
    const [mutate, { data, loading, loaded }] = useMutation();
    const redirect = useRedirect()
    const notify = useNotify();

    const save = React.useCallback(async (values) => {
        try {
            await mutate({
                type: 'update',
                resource: props.resource,
                payload: { id: record.id, data: values }
            }, { returnPromise: true })
        } catch (error) {
            if (error.response.data.errors) {
                return error.response.data.errors;
            }
        }
    }, [mutate])

    React.useEffect(() => {
        if (loaded) {
            notify(`¡Ha editado el modelo "${data.name}" exitosamente!`, 'success')
            redirect('/models')
        }
    }, [loaded])

    const { record } = editControllerProps

    return (
        <BaseForm
            save={save}
            validate={validateModel}
            record={record}
            saveButtonLabel='Actualizar'
            loading={loading}
            formName="Editar Modelo"
        >
            <InputContainer labelName='Nombre'>
                <TextInput
                    name="name"
                    placeholder="Nombre"
                    fullWidth
                />
            </InputContainer>

            <InputContainer labelName='Marca'>
                <ReferenceInput source="brand_id" reference="brands" >
                    <SelectInput optionText="name" optionValue="id" />
                </ReferenceInput>
            </InputContainer>
        </BaseForm>
    )
}

ModelEdit.defaultProps = {
    basePath: 'models',
    resource: 'models'
}

export default ModelEdit
