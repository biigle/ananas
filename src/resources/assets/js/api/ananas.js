import { Resource } from '../import.js';

/**
 * Resource for annotation assistance requests.
 *
 * var resource = biigle.$require('api.ananas');
 *
 * Respond to an assistance request:
 * resource.respond({token: token}, {
 *     response_label_id: 123,
 *     response_text: 'My text..',
 * }).then(...);
 *
 * Delete an assistance request:
 * resource.delete({id: 1).then(...);
 */
export default Resource('api/v1/annotation-assistance-requests{/id}', {}, {
    respond: {
        method: 'PUT',
        url: 'api/v1/annotation-assistance-requests{/token}',
    },
});
