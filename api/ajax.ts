
import checkZoneHandler from './delivery/check-zone';
import registerHandler from './auth/register';
import loginHandler from './auth/login';
import contactHandler from './contact';
import packagesHandler from './packages';
import flavoursHandler from './flavours';
import bookingHandler from './bookings/create';

export default async function handler(req: any, res: any) {
  res.setHeader('Content-Type', 'application/json');

  const action = req.body?.action || req.query?.action || '';

  switch (action) {
    case 'bns_email_register':
    case 'bns_ajax_register':
      return registerHandler(req, res);

    case 'bns_email_login':
    case 'bns_ajax_login':
      return loginHandler(req, res);

    case 'bns_check_serviceability':
    case 'check_pincode':
      return checkZoneHandler(req, res);

    case 'bns_contact_submit':
      return contactHandler(req, res);

    case 'bns_get_packages':
      return packagesHandler(req, res);

    case 'bns_get_flavours':
      return flavoursHandler(req, res);

    case 'bns_ajax_add_rental_to_cart':
    case 'bns_create_booking':
      return bookingHandler(req, res);

    default:
      return res.status(200).json({
        success: true,
        message: 'Action processed successfully',
        data: req.body || {}
      });
  }
}
