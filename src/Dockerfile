# Sử dụng Node.js LTS version
FROM node:20-alpine

# Thiết lập working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Cài đặt dependencies
RUN npm install

# Copy toàn bộ source code
COPY . .

# Expose port
EXPOSE 3000

# Chạy ứng dụng
CMD ["node", "server.js"]
