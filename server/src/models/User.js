import mongoose from 'mongoose';
const userSchema = new mongoose.Schema({
  username: { type: String, required: true, unique: true, trim: true, minlength: 3, maxlength: 24 },
  email: { type: String, required: true, unique: true, lowercase: true, trim: true },
  passwordHash: { type: String, required: true }, avatar: String, country: String,
  xp: { type: Number, default: 0 }, coins: { type: Number, default: 100 }, wins: { type: Number, default: 0 }, losses: { type: Number, default: 0 }, lastOnline: Date
}, { timestamps: true });
export default mongoose.model('User', userSchema);
