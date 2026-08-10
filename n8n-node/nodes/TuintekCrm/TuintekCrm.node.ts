import type {
	IDataObject,
	IExecuteFunctions,
	INodeExecutionData,
	INodeType,
	INodeTypeDescription,
} from 'n8n-workflow';

export class TuintekCrm implements INodeType {
	description: INodeTypeDescription = {
		displayName: 'Tuintek CRM',
		name: 'tuintekCrm',
		icon: 'file:tuintek.svg',
		group: ['transform'],
		version: 1,
		subtitle: '={{$parameter["operation"]}}',
		description: 'Create and update leads and contacts in Tuintek CRM',
		defaults: {
			name: 'Tuintek CRM',
		},
		inputs: ['main'],
		outputs: ['main'],
		credentials: [
			{
				name: 'tuintekCrmApi',
				required: true,
			},
		],
		properties: [
			{
				displayName: 'Operation',
				name: 'operation',
				type: 'options',
				noDataExpression: true,
				options: [
					{ name: 'Create Lead', value: 'createLead' },
					{ name: 'Update Lead Status', value: 'updateLeadStatus' },
					{ name: 'Create Contact', value: 'createContact' },
					{ name: 'Update Contact', value: 'updateContact' },
					{ name: 'Create Project', value: 'createProject' },
					{ name: 'Update Project Status', value: 'updateProjectStatus' },
					{ name: 'Debug Credentials', value: 'debugCredentials' },
				],
				default: 'createLead',
			},
			{
				displayName: 'Contact ID',
				name: 'contactId',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['createLead'] } },
			},
			{
				displayName: 'Title',
				name: 'title',
				type: 'string',
				default: '',
				required: true,
				displayOptions: { show: { operation: ['createLead'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'leadId',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['updateLeadStatus'] } },
			},
			{
				displayName: 'Status',
				name: 'status',
				type: 'options',
				options: [
					{ name: 'New', value: 'new' },
					{ name: 'Contacted', value: 'contacted' },
					{ name: 'Qualified', value: 'qualified' },
					{ name: 'Converted', value: 'converted' },
					{ name: 'Lost', value: 'lost' },
				],
				default: 'new',
				displayOptions: { show: { operation: ['updateLeadStatus'] } },
			},
			{
				displayName: 'First Name',
				name: 'firstName',
				type: 'string',
				default: '',
				required: true,
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Last Name',
				name: 'lastName',
				type: 'string',
				default: '',
				required: true,
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Email',
				name: 'email',
				type: 'string',
				default: '',
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Contact Type',
				name: 'contactType',
				type: 'options',
				options: [
					{ name: 'Customer', value: 'customer' },
					{ name: 'Prospect', value: 'prospect' },
				],
				default: 'prospect',
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Phone',
				name: 'phone',
				type: 'string',
				default: '',
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Company',
				name: 'company',
				type: 'string',
				default: '',
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Owner ID',
				name: 'ownerId',
				type: 'number',
				default: 0,
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'contactIdToUpdate',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['updateContact'] } },
			},
			{
				displayName: 'New Type',
				name: 'newContactType',
				type: 'options',
				options: [
					{ name: 'Customer', value: 'customer' },
					{ name: 'Prospect', value: 'prospect' },
				],
				default: 'prospect',
				displayOptions: { show: { operation: ['updateContact'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'projectLeadId',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'projectContactId',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Project Name',
				name: 'projectName',
				type: 'string',
				default: '',
				required: true,
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Owner ID',
				name: 'projectOwnerId',
				type: 'number',
				default: 0,
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Project ID',
				name: 'projectIdToUpdate',
				type: 'number',
				default: 0,
				required: true,
				displayOptions: { show: { operation: ['updateProjectStatus'] } },
			},
			{
				displayName: 'Status',
				name: 'projectStatus',
				type: 'options',
				options: [
					{ name: 'Planning', value: 'planning' },
					{ name: 'Active', value: 'active' },
					{ name: 'Completed', value: 'completed' },
					{ name: 'Cancelled', value: 'cancelled' },
				],
				default: 'planning',
				displayOptions: { show: { operation: ['updateProjectStatus'] } },
			},
		],
	};

	async execute(this: IExecuteFunctions): Promise<INodeExecutionData[][]> {
		const items = this.getInputData();
		const returnData: INodeExecutionData[] = [];
		const operation = this.getNodeParameter('operation', 0) as string;

		const credentials = await this.getCredentials('tuintekCrmApi');
		const baseUrl = (credentials.baseUrl as string).trim().replace(/\/+$/, '');
		const tokenId = (credentials.tokenId as string).trim();
		const tokenSecret = (credentials.tokenSecret as string).trim();
		const token = `${tokenId}|${tokenSecret}`;

		if (operation === 'debugCredentials') {
			let testResult: IDataObject;
			try {
				const response = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/leads`,
					headers: { Authorization: `Bearer ${token}` },
					json: true,
					returnFullResponse: true,
				});
				testResult = {
					success: true,
					statusCode: response.statusCode,
					body: response.body,
				};
			} catch (err: any) {
				testResult = {
					success: false,
					errorMessage: err.message,
					errorStatusCode: err.response?.status ?? err.statusCode ?? null,
					errorBody: err.response?.data ?? err.cause?.response?.data ?? null,
					requestHeadersSent: err.config?.headers ?? null,
					requestUrlUsed: err.config?.url ?? null,
				};
			}

			return [[{
				json: {
					baseUrl,
					tokenId,
					tokenSecretLength: tokenSecret.length,
					fullTokenUsed: token,
					fullUrlThatWouldBeUsed: `${baseUrl}/api/leads`,
					testResult,
				},
			}]];
		}

		for (let i = 0; i < items.length; i++) {
			let responseData;

			if (operation === 'createLead') {
				const contactId = this.getNodeParameter('contactId', i) as number;
				const title = this.getNodeParameter('title', i) as string;

				responseData = await this.helpers.httpRequest({
					method: 'POST',
					url: `${baseUrl}/api/leads`,
					headers: { Authorization: `Bearer ${token}` },
					body: { contact_id: contactId, title, status: 'new' },
					json: true,
				});
			}

			if (operation === 'updateLeadStatus') {
				const leadId = this.getNodeParameter('leadId', i) as number;
				const status = this.getNodeParameter('status', i) as string;

				responseData = await this.helpers.httpRequest({
					method: 'PUT',
					url: `${baseUrl}/api/leads/${leadId}`,
					headers: { Authorization: `Bearer ${token}` },
					body: { status },
					json: true,
				});
			}

			if (operation === 'createContact') {
				const firstName = this.getNodeParameter('firstName', i) as string;
				const lastName = this.getNodeParameter('lastName', i) as string;
				const email = this.getNodeParameter('email', i) as string;
				const contactType = this.getNodeParameter('contactType', i) as string;
				const phone = this.getNodeParameter('phone', i) as string;
				const company = this.getNodeParameter('company', i) as string;
				const ownerId = this.getNodeParameter('ownerId', i) as number;

				responseData = await this.helpers.httpRequest({
					method: 'POST',
					url: `${baseUrl}/api/contacts`,
					headers: { Authorization: `Bearer ${token}` },
					body: {
						first_name: firstName,
						last_name: lastName,
						email,
						type: contactType,
						phone: phone || undefined,
						company: company || undefined,
						owner_id: ownerId || undefined,
					},
					json: true,
				});
			}

			if (operation === 'updateContact') {
				const contactIdToUpdate = this.getNodeParameter('contactIdToUpdate', i) as number;
				const newContactType = this.getNodeParameter('newContactType', i) as string;

				responseData = await this.helpers.httpRequest({
					method: 'PUT',
					url: `${baseUrl}/api/contacts/${contactIdToUpdate}`,
					headers: { Authorization: `Bearer ${token}` },
					body: { type: newContactType },
					json: true,
				});
			}

			if (operation === 'createProject') {
				const leadId = this.getNodeParameter('projectLeadId', i) as number;
				const contactId = this.getNodeParameter('projectContactId', i) as number;
				const name = this.getNodeParameter('projectName', i) as string;
				const ownerId = this.getNodeParameter('projectOwnerId', i) as number;

				responseData = await this.helpers.httpRequest({
					method: 'POST',
					url: `${baseUrl}/api/projects`,
					headers: { Authorization: `Bearer ${token}` },
					body: {
						lead_id: leadId,
						contact_id: contactId,
						name,
						status: 'planning',
						owner_id: ownerId || undefined,
					},
					json: true,
				});
			}

			if (operation === 'updateProjectStatus') {
				const projectId = this.getNodeParameter('projectIdToUpdate', i) as number;
				const status = this.getNodeParameter('projectStatus', i) as string;

				responseData = await this.helpers.httpRequest({
					method: 'PUT',
					url: `${baseUrl}/api/projects/${projectId}`,
					headers: { Authorization: `Bearer ${token}` },
					body: { status },
					json: true,
				});
			}

			returnData.push({ json: responseData as IDataObject });
		}

		return [returnData];
	}
}