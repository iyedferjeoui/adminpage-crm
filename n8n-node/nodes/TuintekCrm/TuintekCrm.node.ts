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
		description: 'Manage Tuintek CRM records. Use Create Contact for a new person or company you have not seen before. Use Find Contact / Get Contact to look up existing contacts by filters or by ID. Use Create Lead when an existing contact expresses interest in something (a request, question, or potential sale) — requires an existing Contact ID. Use Find Lead / Get Lead to look up existing leads. Use Update Lead Status to move a lead through its pipeline (new, contacted, qualified, converted, lost). Use Create Project when a lead has been converted into confirmed work — requires an existing Lead ID and Contact ID. Use Find Project / Get Project to look up existing projects. Use Update Project Status to change a project\'s stage (planning, active, completed, cancelled). Use Update Contact to change an existing contact\'s type (customer vs prospect).',
		defaults: {
			name: 'Tuintek CRM',
		},
		inputs: ['main'],
		outputs: ['main'],
		usableAsTool: true,
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
				description: 'Choose what action to perform in the CRM',
				options: [
					{ name: 'Create Lead', value: 'createLead', description: 'Create a new sales lead linked to an existing contact' },
					{ name: 'Update Lead Status', value: 'updateLeadStatus', description: 'Change the status of an existing lead' },
					{ name: 'Find Lead', value: 'findLead', description: 'Search for leads matching filters' },
					{ name: 'Get Lead', value: 'getLead', description: 'Retrieve a single lead by ID' },
					{ name: 'Create Contact', value: 'createContact', description: 'Create a new contact (person or company)' },
					{ name: 'Update Contact', value: 'updateContact', description: "Update an existing contact's type" },
					{ name: 'Find Contact', value: 'findContact', description: 'Search for contacts matching filters' },
					{ name: 'Get Contact', value: 'getContact', description: 'Retrieve a single contact by ID' },
					{ name: 'Create Project', value: 'createProject', description: 'Create a new project linked to a lead and contact' },
					{ name: 'Update Project Status', value: 'updateProjectStatus', description: "Change the status of an existing project" },
					{ name: 'Find Project', value: 'findProject', description: 'Search for projects matching filters' },
					{ name: 'Get Project', value: 'getProject', description: 'Retrieve a single project by ID' },
					{ name: 'Debug Credentials', value: 'debugCredentials', description: 'Test that the API credentials work' },
				],
				default: 'createLead',
			},
			{
				displayName: 'Contact ID',
				name: 'contactId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the existing contact this lead belongs to',
				displayOptions: { show: { operation: ['createLead'] } },
			},
			{
				displayName: 'Title',
				name: 'title',
				type: 'string',
				default: '',
				required: true,
				description: 'Short title describing what the lead is about (e.g. what the person is asking for)',
				displayOptions: { show: { operation: ['createLead'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'leadId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the lead to update',
				displayOptions: { show: { operation: ['updateLeadStatus'] } },
			},
			{
				displayName: 'Status',
				name: 'status',
				type: 'options',
				description: 'New status to set on the lead',
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
				displayName: 'Contact ID',
				name: 'findLeadContactId',
				type: 'number',
				default: 0,
				description: 'Filter leads belonging to this contact (leave at 0 to match any contact)',
				displayOptions: { show: { operation: ['findLead'] } },
			},
			{
				displayName: 'Status',
				name: 'findLeadStatus',
				type: 'options',
				description: 'Filter leads by status',
				options: [
					{ name: 'Any', value: 'any' },
					{ name: 'New', value: 'new' },
					{ name: 'Contacted', value: 'contacted' },
					{ name: 'Qualified', value: 'qualified' },
					{ name: 'Converted', value: 'converted' },
					{ name: 'Lost', value: 'lost' },
				],
				default: 'any',
				displayOptions: { show: { operation: ['findLead'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'getLeadId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the lead to retrieve',
				displayOptions: { show: { operation: ['getLead'] } },
			},
			{
				displayName: 'First Name',
				name: 'firstName',
				type: 'string',
				default: '',
				required: true,
				description: "The contact's first name",
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Last Name',
				name: 'lastName',
				type: 'string',
				default: '',
				required: true,
				description: "The contact's last name",
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Email',
				name: 'email',
				type: 'string',
				default: '',
				description: "The contact's email address",
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Contact Type',
				name: 'contactType',
				type: 'options',
				description: 'Whether this contact is an existing customer or a new prospect',
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
				description: "The contact's phone number, if known",
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Company',
				name: 'company',
				type: 'string',
				default: '',
				description: "The contact's company name, if known",
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Owner ID',
				name: 'ownerId',
				type: 'number',
				default: 0,
				description: 'ID of the user who should own this contact, if known',
				displayOptions: { show: { operation: ['createContact'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'contactIdToUpdate',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the contact to update',
				displayOptions: { show: { operation: ['updateContact'] } },
			},
			{
				displayName: 'New Type',
				name: 'newContactType',
				type: 'options',
				description: 'New type to set on the contact',
				options: [
					{ name: 'Customer', value: 'customer' },
					{ name: 'Prospect', value: 'prospect' },
				],
				default: 'prospect',
				displayOptions: { show: { operation: ['updateContact'] } },
			},
			{
				displayName: 'Email',
				name: 'findContactEmail',
				type: 'string',
				default: '',
				description: 'Filter contacts by email (leave blank to match any email)',
				displayOptions: { show: { operation: ['findContact'] } },
			},
			{
				displayName: 'Contact Type',
				name: 'findContactType',
				type: 'options',
				description: 'Filter contacts by type',
				options: [
					{ name: 'Any', value: 'any' },
					{ name: 'Customer', value: 'customer' },
					{ name: 'Prospect', value: 'prospect' },
				],
				default: 'any',
				displayOptions: { show: { operation: ['findContact'] } },
			},
			{
				displayName: 'Search Query',
				name: 'findContactQuery',
				type: 'string',
				default: '',
				description: 'Free-text search (e.g. name or company), if your CRM API supports it',
				displayOptions: { show: { operation: ['findContact'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'getContactId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the contact to retrieve',
				displayOptions: { show: { operation: ['getContact'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'projectLeadId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the lead this project originated from',
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'projectContactId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the contact this project belongs to',
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Project Name',
				name: 'projectName',
				type: 'string',
				default: '',
				required: true,
				description: 'Name of the project',
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Owner ID',
				name: 'projectOwnerId',
				type: 'number',
				default: 0,
				description: 'ID of the user who should own this project, if known',
				displayOptions: { show: { operation: ['createProject'] } },
			},
			{
				displayName: 'Project ID',
				name: 'projectIdToUpdate',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the project to update',
				displayOptions: { show: { operation: ['updateProjectStatus'] } },
			},
			{
				displayName: 'Status',
				name: 'projectStatus',
				type: 'options',
				description: 'New status to set on the project',
				options: [
					{ name: 'Planning', value: 'planning' },
					{ name: 'Active', value: 'active' },
					{ name: 'Completed', value: 'completed' },
					{ name: 'Cancelled', value: 'cancelled' },
				],
				default: 'planning',
				displayOptions: { show: { operation: ['updateProjectStatus'] } },
			},
			{
				displayName: 'Contact ID',
				name: 'findProjectContactId',
				type: 'number',
				default: 0,
				description: 'Filter projects belonging to this contact (leave at 0 to match any contact)',
				displayOptions: { show: { operation: ['findProject'] } },
			},
			{
				displayName: 'Lead ID',
				name: 'findProjectLeadId',
				type: 'number',
				default: 0,
				description: 'Filter projects originating from this lead (leave at 0 to match any lead)',
				displayOptions: { show: { operation: ['findProject'] } },
			},
			{
				displayName: 'Status',
				name: 'findProjectStatus',
				type: 'options',
				description: 'Filter projects by status',
				options: [
					{ name: 'Any', value: 'any' },
					{ name: 'Planning', value: 'planning' },
					{ name: 'Active', value: 'active' },
					{ name: 'Completed', value: 'completed' },
					{ name: 'Cancelled', value: 'cancelled' },
				],
				default: 'any',
				displayOptions: { show: { operation: ['findProject'] } },
			},
			{
				displayName: 'Project ID',
				name: 'getProjectId',
				type: 'number',
				default: 0,
				required: true,
				description: 'The ID of the project to retrieve',
				displayOptions: { show: { operation: ['getProject'] } },
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

			if (operation === 'findLead') {
				const contactId = this.getNodeParameter('findLeadContactId', i) as number;
				const status = this.getNodeParameter('findLeadStatus', i) as string;

				const qs: IDataObject = {};
				if (contactId) qs.contact_id = contactId;
				if (status && status !== 'any') qs.status = status;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/leads`,
					headers: { Authorization: `Bearer ${token}` },
					qs,
					json: true,
				});
			}

			if (operation === 'getLead') {
				const leadId = this.getNodeParameter('getLeadId', i) as number;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/leads/${leadId}`,
					headers: { Authorization: `Bearer ${token}` },
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

			if (operation === 'findContact') {
				const email = this.getNodeParameter('findContactEmail', i) as string;
				const contactType = this.getNodeParameter('findContactType', i) as string;
				const query = this.getNodeParameter('findContactQuery', i) as string;

				const qs: IDataObject = {};
				if (email) qs.email = email;
				if (contactType && contactType !== 'any') qs.type = contactType;
				if (query) qs.search = query;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/contacts`,
					headers: { Authorization: `Bearer ${token}` },
					qs,
					json: true,
				});
			}

			if (operation === 'getContact') {
				const contactId = this.getNodeParameter('getContactId', i) as number;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/contacts/${contactId}`,
					headers: { Authorization: `Bearer ${token}` },
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

			if (operation === 'findProject') {
				const contactId = this.getNodeParameter('findProjectContactId', i) as number;
				const leadId = this.getNodeParameter('findProjectLeadId', i) as number;
				const status = this.getNodeParameter('findProjectStatus', i) as string;

				const qs: IDataObject = {};
				if (contactId) qs.contact_id = contactId;
				if (leadId) qs.lead_id = leadId;
				if (status && status !== 'any') qs.status = status;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/projects`,
					headers: { Authorization: `Bearer ${token}` },
					qs,
					json: true,
				});
			}

			if (operation === 'getProject') {
				const projectId = this.getNodeParameter('getProjectId', i) as number;

				responseData = await this.helpers.httpRequest({
					method: 'GET',
					url: `${baseUrl}/api/projects/${projectId}`,
					headers: { Authorization: `Bearer ${token}` },
					json: true,
				});
			}

			if (Array.isArray(responseData)) {
				if (responseData.length === 0) {
					returnData.push({ json: { found: false, message: 'No matching records found' } });
				} else {
					for (const record of responseData) {
						returnData.push({ json: record as IDataObject });
					}
				}
			} else {
				returnData.push({ json: responseData as IDataObject });
			}
		}

		return [returnData];
	}
}