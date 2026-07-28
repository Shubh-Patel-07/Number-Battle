FROM node:24-alpine
WORKDIR /app
COPY server/package.json ./package.json
RUN npm install --omit=dev
COPY server/src ./src
EXPOSE 4000
CMD ["npm", "start"]
