import jwt from 'jsonwebtoken';
export const tokenFor = user => jwt.sign({ sub: user.id, username: user.username }, process.env.JWT_SECRET, { expiresIn: '7d' });
export function requireAuth(req, res, next) { try { const token = req.headers.authorization?.replace('Bearer ', ''); req.user = jwt.verify(token, process.env.JWT_SECRET); next(); } catch { res.status(401).json({ error: 'Authentication required' }); } }
