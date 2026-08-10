import {
	ICredentialType,
	INodeProperties,
} from 'n8n-workflow';

export class TuintekCrmApi implements ICredentialType {
	name = 'tuintekCrmApi';
	displayName = 'Tuintek CRM API';
	properties: INodeProperties[] = [
		{
			displayName: 'Base URL',
			name: 'baseUrl',
			type: 'string',
			default: 'http://tuintek-crm.test',
			placeholder: 'http://tuintek-crm.test',
			description: 'The base URL of your Tuintek CRM instance',
		},
		{
			displayName: 'Token ID',
			name: 'tokenId',
			type: 'string',
			default: '',
			description: 'The number before the | in your token, e.g. 10',
		},
		{
			displayName: 'Token Secret',
			name: 'tokenSecret',
			type: 'string',
			typeOptions: {
				password: true,
			},
			default: '',
			description: 'The random string after the | in your token',
		},
	];
}